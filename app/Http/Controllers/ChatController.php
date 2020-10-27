<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\ChatMessage;
use App\ChatGroup;
use App\ChatGroupUser;
use App\GroupChatMessage;
use App\GroupChatMessageStatus;
use DB;
use Auth;
use Validator;

class ChatController extends Controller
{
	public $pusher;
	
	public function __construct()
    {
		//Don't allow Super admin to access 
		$this->middleware(function ($request, $next) {
			if(Auth::user()->user_type == "admin"){
				return redirect('dashboard');	
			}

			if(Auth::user()->user_type == "client"){
				return $next($request);	
			}
			
			if(get_option('live_chat') != 'enabled'){
				return redirect('dashboard');	
			}

			if( has_membership_system() == 'enabled' ){
                if( ! has_feature( 'live_chat' ) ){
                    return redirect('membership/extend')->with('message',_lang('Your Current package not support this feature. You can upgrade your package !'));
                }
            }    

			return $next($request);
		});
		
        date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));		
		
		$options = array(
			'cluster' => get_option('PUSHER_CLUSTER'),
			'useTLS' => true
		);
		$this->pusher = new \Pusher\Pusher(
			get_option('PUSHER_KEY'),
			get_option('PUSHER_SECRET'),
			get_option('PUSHER_APP_ID'),
			$options
		);
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$company_id = company_id();
		$user_id = Auth::user()->id;
		$data = array();
		
		if(Auth::user()->user_type != 'client'){
			$unreadMessages = DB::table('chat_messages')
							    ->select('from','to', DB::raw('COUNT(id) as unread_message_count'))   
							    ->where('company_id', $company_id)
								->where('status', 0)
							    ->groupBy('from');
			   
			
			$data['staffs'] = DB::table('users')
			                    ->leftJoinSub($unreadMessages, 'unread_messages', function ($join) use($user_id) {
									$join->on('users.id', '=', 'unread_messages.from')
									     ->where('unread_messages.to',$user_id);
								})
								->where('user_type','!=','client')
								->whereRaw("company_id = $company_id AND id != $user_id")
								->get();
								
			
			$data['clients'] = DB::table('users')
			                       ->leftJoinSub($unreadMessages, 'unread_messages', function ($join) use($user_id) {
									   $join->on('users.id', '=', 'unread_messages.from')
									        ->where('unread_messages.to',$user_id);
								   })
								   ->select('users.*','unread_messages.*')
			                       ->join('contacts','contacts.user_id','users.id')
								   ->where('user_type','client')
								   ->where('contacts.company_id',$company_id)
								   ->where('users.id','!=',$user_id)
								   ->get();
							   
		}else{
			$company_ids = Auth::user()->client->pluck('company_id');

			if(count($company_ids) == 0){
				return back()->with('error',_lang('Sorry, Currently no business associated with your account !'));
			}


        	$unreadMessages = DB::table('chat_messages')
								->select('from','to', DB::raw('COUNT(id) as unread_message_count'))   
								->whereIn('company_id', $company_ids)
								->where('status', 0)
								->groupBy('from');
		   
		
			$data['staffs'] = DB::table('users')
								->leftJoinSub($unreadMessages, 'unread_messages', function ($join) use($user_id) {
									$join->on('users.id', '=', 'unread_messages.from')
										 ->where('unread_messages.to',$user_id);
								})
								->where('user_type','!=','client')
								->whereIn('company_id', $company_ids)
								->get();
		}

        $data['chat_groups'] = User::find($user_id)->chat_groups;			

		return view('backend.live_chat.chat',$data);
	}
    
	
	//Send User to User/Client Message
	public function send_message(Request $request)
    {
		if ($request->hasFile('file')){
			$this->upload_file($request);
		}else{
			$validator = Validator::make($request->all(), [
				'to' => "required",
				'message' => "required",
			]);
			
			if ($validator->fails()) {
				echo json_encode(['result'=>false,'message'=>$validator->errors()->all()]);				
			    die();
			}
			
			$chat_message = new ChatMessage();
			$chat_message->from = Auth::user()->id;
			$chat_message->to = intval($request->to);
			
			//If user Directly enter a tag
			if(strpos($request->message, '<a') !== false){
				echo json_encode(['result'=>false,'message'=>array('msg'=>'illegal operation')]);	
			    die();
			}else{
				//Make website to URL
				$url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
				$msg_string = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $request->message);
				$chat_message->message = strip_tags($msg_string,"<a><br>");
	        }
			
		    //Remove inline Style
		    $chat_message->message = preg_replace('/(<[^>]*) style=("[^"]+"|\'[^\']+\')([^>]*>)/i', '$1$3', $chat_message->message);
			
			$chat_message->company_id = company_id();
			$chat_message->save();
			$chat_message->attachment = false;
			
			$this->pusher->trigger('presence-mychanel', 'message-event', $chat_message);
			echo json_encode(['result'=>true,'data'=>$chat_message]);	
		}
	}
	
	//Send Group Message
	public function send_group_message(Request $request)
    {
		if ($request->hasFile('file')){
			$this->group_upload_file($request);
		}else{
			$validator = Validator::make($request->all(), [
				'group' => "required",
				'message' => "required",
			]);
			
			if ($validator->fails()) {
				echo json_encode(['result'=>false,'message'=>$validator->errors()->all()]);				
			    die();
			}
			
			//Security Check
			$user = Auth::user();
			$exists = $user->chat_groups()->where('chat_groups.id', $request->group)->exists();

			if($exists == false){
				echo json_encode(['result'=>false,'message'=>array('error'=>_lang('Illegal Operation !'))]);				
			    die();
			}

			$chat_message = new GroupChatMessage();
			$chat_message->group_id = intval($request->group);
			$chat_message->sender_id = Auth::user()->id;
			
			//If user Directly enter a tag
			if(strpos($request->message, '<a') !== false){
				echo json_encode(['result'=>false,'message'=>array('msg'=>'illegal operation')]);	
			    die();
			}else{
				//Make website to URL
				$url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
				$msg_string = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $request->message);
				$chat_message->message = strip_tags($msg_string,"<a><br>");
	        }
			
		    //Remove inline Style
		    $chat_message->message = preg_replace('/(<[^>]*) style=("[^"]+"|\'[^\']+\')([^>]*>)/i', '$1$3', $chat_message->message);
			
			$chat_message->company_id = company_id();
			$chat_message->save();
			$chat_message->attachment = false;
			$chat_message->sender = Auth::user()->name;
			$chat_message->group_members = $chat_message->group->group_members->pluck('id');
			
			$this->pusher->trigger('group-channel', 'group-message-event', $chat_message);
			echo json_encode(['result'=>true,'data'=>$chat_message]);	
		}
	}
	
	private function upload_file(Request $request){
		$max_size = get_option('chat_max_upload_size',2)*1024;
		$supported_file_types = get_option('chat_file_type_supported','png,jpg,jpeg');
		
		$validator = Validator::make($request->all(), [
			'to' => "required",
			'file' => "required|max:$max_size|mimes:$supported_file_types",
		],[
		    'mimes' => 'File type is not supported',
		]);
		
		if ($validator->fails()) {
			echo json_encode(['result'=>false,'message'=>$validator->errors()->all()]);				
		    die();
		}
		
		$file = $request->file('file');
		$file_name = "Attachment_".time().'.'.$file->getClientOriginalExtension();
		$file->move(base_path('public/uploads/chat_files/'),$file_name);
		$orginal_name = $file->getClientOriginalName();
		
		$msg_text = "<a target='_blank' href='".asset("public/uploads/chat_files/$file_name")."'>$orginal_name</a>";
		//$msg_text = "<img class='' src='".asset("public/uploads/chat_files/$file_name")."'>";

			
		$chat_message = new ChatMessage();
		$chat_message->from = Auth::user()->id;
		$chat_message->to = intval($request->to);
		
		if($request->message != ''){
			//If user Directly enter a tag
			if(strpos($request->message, '<a') !== false){
				echo json_encode(['result'=>false,'message'=>array('msg'=>'illegal operation')]);	
			    die();
			}else{
				//Make website to URL
				$url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
				$msg_string = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $request->message);
				$message = strip_tags($msg_string,"<a><br><img>");
				$msg_text = $message."</br><a target='_blank' href='".asset("public/uploads/chat_files/$file_name")."'>$file_name</a>";
	        }
	
		}
		$chat_message->message = strip_tags($msg_text,"<a><br><img>");
		//Remove inline Style
		$chat_message->message = preg_replace('/(<[^>]*) style=("[^"]+"|\'[^\']+\')([^>]*>)/i', '$1$3', $chat_message->message);		
		$chat_message->attachment = $file_name;
		$chat_message->company_id = company_id();
		$chat_message->save();
		$chat_message->attachment = true;
		
		$this->pusher->trigger('presence-mychanel', 'message-event', $chat_message);
		echo json_encode(['result'=>true,'data'=>$chat_message]);
	    
	}
	
	private function group_upload_file(Request $request){
		$max_size = get_option('chat_max_upload_size',2)*1024;
		$supported_file_types = get_option('chat_file_type_supported','png,jpg,jpeg');
		
		$validator = Validator::make($request->all(), [
			'group' => "required",
			'file' => "required|max:$max_size|mimes:$supported_file_types",
		],[
		    'mimes' => 'File type is not supported',
		]);
		
		if ($validator->fails()) {
			echo json_encode(['result'=>false,'message'=>$validator->errors()->all()]);				
		    die();
		}
		
		//Security Check
		$user = Auth::user();
		$exists = $user->chat_groups()->where('chat_groups.id', $request->group)->exists();

		if($exists == false){
			echo json_encode(['result'=>false,'message'=>array('error'=>_lang('Illegal Operation !'))]);				
			die();
		}
		
		$file = $request->file('file');
		$file_name = "Attachment_".time().'.'.$file->getClientOriginalExtension();
		$file->move(base_path('public/uploads/chat_files/'),$file_name);
		$orginal_name = $file->getClientOriginalName();
		
		$msg_text = "<a target='_blank' href='".asset("public/uploads/chat_files/$file_name")."'>$orginal_name</a>";

				
		$chat_message = new GroupChatMessage();
		$chat_message->group_id = intval($request->group);
		$chat_message->sender_id = Auth::user()->id;
		
		if($request->message != ''){
			//If user Directly enter a tag
			if(strpos($request->message, '<a') !== false){
				echo json_encode(['result'=>false,'message'=>array('msg'=>'illegal operation')]);	
			    die();
			}else{
				//Make website to URL
				$url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
				$msg_string = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $request->message);
				$message = strip_tags($msg_string,"<a><br>");
				$msg_text = $message."</br><a target='_blank' href='".asset("public/uploads/chat_files/$file_name")."'>$file_name</a>";
	        }
	
		}
		$chat_message->message = strip_tags($msg_text,"<a><br>");
		//Remove inline Style
		$chat_message->message = preg_replace('/(<[^>]*) style=("[^"]+"|\'[^\']+\')([^>]*>)/i', '$1$3', $chat_message->message);		
		$chat_message->attachment = $file_name;
		$chat_message->company_id = company_id();
		$chat_message->save();
		$chat_message->attachment = true;
		$chat_message->sender = Auth::user()->name;
		$chat_message->group_members = $chat_message->group->group_members->pluck('id');	
		
		$this->pusher->trigger('group-channel', 'group-message-event', $chat_message);
		echo json_encode(['result'=>true,'data'=>$chat_message]);
	    
	}
	
	// Get All messages after clicking sidebar //
	public function get_messages($user_id, $limit=30, $offset =0)
    {
		DB::beginTransaction();
		$me = Auth::user()->id;
		$messages = DB::select("SELECT * FROM `chat_messages` WHERE (chat_messages.from = $user_id AND chat_messages.to = $me) OR (chat_messages.from = $me AND chat_messages.to = $user_id) ORDER BY id DESC LIMIT $limit OFFSET $offset");
		
		$unread_messages = ChatMessage::where('company_id',company_id())
		                              ->where('status',0)
									  ->where('from',$user_id)
									  ->update(['status' => 1]);
		
		DB::commit();
		echo json_encode($messages);                            
	}
	
	// Get All messages after clicking sidebar //
	public function get_group_messages($group_id, $limit=30, $offset =0)
    {
		DB::beginTransaction();
		$user = Auth::user();
		$me = $user->id;
		$company_id = company_id();
		
		//Security Check
		$exists = $user->chat_groups()->where('chat_groups.id', $group_id)->exists();

		if($exists == false){
			die();
		}
		
		if($user->user_type != 'client'){
			$messages = DB::select("SELECT group_chat_messages.* FROM `group_chat_messages` WHERE group_id = $group_id AND company_id=$company_id ORDER BY id DESC LIMIT $limit OFFSET $offset");
		}else{
			$company_ids = $user->client->pluck('company_id')->toArray();
		 	$company_ids = implode(",",$company_ids);

			$messages = DB::select("SELECT group_chat_messages.* FROM `group_chat_messages` WHERE group_id = $group_id AND company_id IN ($company_ids) ORDER BY id DESC LIMIT $limit OFFSET $offset");
		}
		
		//Unread Message Operation	
		$read_messages = GroupChatMessageStatus::where('user_id',$me)
		                                       ->where('group_id',$group_id)
		                                       ->orderBy('id','desc')
											   ->limit(1)
											   ->get();
		
		$read_message_id = 0;									   
		if( ! $read_messages->isEmpty() ){
			$read_message_id = $read_messages[0]->message_id;
		}								   
		
		if($user->user_type != 'client'){
			$unread_messages = GroupChatMessage::where('company_id',company_id())
			                                   ->where('group_id',$group_id)
										       ->where('sender_id','!=',$me)
											   ->where('id','>',$read_message_id)
											   ->get();
		}else{
			$company_ids = $user->client->pluck('company_id')->toArray();
			$unread_messages = GroupChatMessage::whereIn('company_id',$company_ids)
			                                   ->where('group_id',$group_id)
										       ->where('sender_id','!=',$me)
											   ->where('id','>',$read_message_id)
											   ->get();
		}								   
		
		foreach($unread_messages as $um){
			$message_status = new GroupChatMessageStatus();
			$message_status->message_id = $um->id;
			$message_status->group_id = $um->group_id;
			$message_status->user_id = $me;
			$message_status->company_id = $um->company_id;
			$message_status->save();
		}
		
		DB::commit();
		echo json_encode($messages);                            
	}
	
	public function mark_as_read($sender_id){
		$me = Auth::user()->id;
		$unread_messages = ChatMessage::where('status',0)
		                              ->where('from',$sender_id)
		                              ->where('to',$me)
									  ->update(['status' => 1]);
	}
	
	public function mark_as_group_read($group_id){
		DB::beginTransaction();
		$me = Auth::user()->id;
		$company_id = company_id();
		
		//Unread Message Operation	
		$read_messages = GroupChatMessageStatus::where('user_id',$me)
		                                       ->where('group_id',$group_id)
		                                       ->orderBy('id','desc')
											   ->limit(1)
											   ->get();
		
		$read_message_id = 0;									   
		if( ! $read_messages->isEmpty() ){
			$read_message_id = $read_messages[0]->message_id;
		}								   
		
		if(Auth::user()->user_type != 'client'){
			$unread_messages = GroupChatMessage::where('company_id',company_id())
			                                   ->where('group_id',$group_id)
										       ->where('sender_id','!=',$me)
											   ->where('id','>',$read_message_id)
											   ->get();

        }else{
        	$company_ids = $user->client->pluck('company_id')->toArray();
		 	$company_ids = implode(",",$company_ids);
		 	$unread_messages = GroupChatMessage::whereIn('company_id',$company_ids)
			                                   ->where('group_id',$group_id)
										       ->where('sender_id','!=',$me)
											   ->where('id','>',$read_message_id)
											   ->get();
        }
										   
		foreach($unread_messages as $um){
			$message_status = new GroupChatMessageStatus();
			$message_status->message_id = $um->id;
			$message_status->group_id = $um->group_id;
			$message_status->user_id = $me;
			$message_status->company_id = $um->company_id;
			$message_status->save();
		}
		
		DB::commit();								   
	}
	
	//Authenticating Presence User to User channels
	public function auth(Request $request)
    {
		echo $this->pusher->presence_auth($request->channel_name,$request->socket_id, Auth::user()->id, Auth::user());
	}
	
	
	/**************************************************/
	/*------------- Group Chat Functions -------------*/
	/**************************************************/
	public function create_group(Request $request)
    {
		if( $request->ajax()){
		   return view('backend.live_chat.modal.create_group');
		}
	}	
	
	public function store_group(Request $request)
    {
		$validator = Validator::make($request->all(), [
			'name' => "required|max:50",
		]);
		
		if ($validator->fails()) {
			echo json_encode(['result'=>false,'message'=>$validator->errors()->all()]);				
			die();
		}
		
		DB::beginTransaction();
		$chat_group = new ChatGroup();
		$chat_group->name = $request->name;
		$chat_group->description = $request->description;
		$chat_group->company_id = company_id();
		$chat_group->created_by = Auth::user()->id;
		$chat_group->save();
		
		//Create Chat Admin
		$chat_group_user = new ChatGroupUser();
		$chat_group_user->group_id = $chat_group->id;
		$chat_group_user->user_id = Auth::user()->id;
		$chat_group_user->save();
		
		//Update Chat Members
		foreach($request->members as $member){
			$chat_group_user = new ChatGroupUser();
			$chat_group_user->group_id = $chat_group->id;
			$chat_group_user->user_id = $member;
			$chat_group_user->save();
		}

		DB::commit();
		
		$chat_group->img = get_initials($chat_group->name);
		$data = array();
		$data['group'] = $chat_group;
		$data['group_members'] = $chat_group->group_members;
		
		$this->pusher->trigger('group-channel', 'group-create-event', $data);
		
		return response()->json(['result'=>true,'message'=>_lang('New group created'),'data'=>$chat_group,'group_members'=>$chat_group->group_members]);
	}
	
	/*------------ Edit group -------------*/
	public function edit_group(Request $request, $id)
    {
		//Security Check
		$group = ChatGroup::where('id',$id)
						  ->where('created_by',Auth::user()->id)
						  ->where('company_id',company_id())
						  ->first();

		if($group){
			if( $request->ajax()){
			   return view('backend.live_chat.modal.edit_group',compact('group'));
			}
		}
	
	}
	
	/*------------ Update group -------------*/
	public function update_group(Request $request, $id)
    {
		$validator = Validator::make($request->all(), [
			'name' => "required|max:50",
		]);
		
		if ($validator->fails()) {
			echo json_encode(['result'=>false,'message'=>$validator->errors()->all()]);				
			die();
		}
		
		DB::beginTransaction();
		$chat_group = ChatGroup::where('id',$id)
							   ->where('created_by',Auth::user()->id)
							   //->where('company_id',company_id())
							   ->first();

		$chat_group->name = $request->name;
		$chat_group->description = $request->description;
		$chat_group->created_by = Auth::user()->id;
		$chat_group->save();
		
		//Remove Previous Records
		$chat_group_user = ChatGroupUser::where('group_id',$chat_group->id);
		$chat_group_user->delete();
		
		//Create Chat Admin
		$chat_group_user = new ChatGroupUser();
		$chat_group_user->group_id = $chat_group->id;
		$chat_group_user->user_id = Auth::user()->id;
		$chat_group_user->save();
		
		//Update Chat Members
		foreach($request->members as $member){
			$chat_group_user = new ChatGroupUser();
			$chat_group_user->group_id = $chat_group->id;
			$chat_group_user->user_id = $member;
			$chat_group_user->save();
		}

		DB::commit();
		
		$chat_group->img = get_initials($chat_group->name);
		$data = array();
		$data['group'] = $chat_group;
		$data['group_members'] = $chat_group->group_members;
		$data['last_message'] = get_last_group_message($chat_group->id);
		
		$this->pusher->trigger('group-channel', 'group-update-event', $data);
		
		return response()->json(['result'=>true,'message'=>_lang('Group updated'),'data'=>$chat_group,'group_members'=>$chat_group->group_members]);
	
	}
	
	
	/*------------ View Group Members-------------*/
	public function view_group_members(Request $request, $id)
    {
		//Security Check
		$group = Auth::user()->chat_groups()->where('chat_groups.id', $id);

		if($group->exists() == false){
			die();
		}
        
		$group = $group->first();
		
		if( $request->ajax()){
		   return view('backend.live_chat.modal.group_members',compact('group'));
		}

	}
	
	/*------------ Edit group -------------*/
	public function delete_group(Request $request, $id)
    {
		//Security Check
		$group = ChatGroup::where('id',$id)
						  ->where('created_by',Auth::user()->id)
						  //->where('company_id',company_id())
						  ->first();

		if($group){
			$group->delete();
			
			$chat_group_user = ChatGroupUser::where('group_id',$id);
			$chat_group_user->delete();
			
			$this->pusher->trigger('group-channel', 'group-delete-event', $id);
		    return response()->json(['result'=>true,'message'=>_lang('Group Removed')]);
		}
	
	}
	
	/*------------ Left group -------------*/
	public function left_group(Request $request, $id)
    {
		DB::beginTransaction();
		//Security Check
		$user = Auth::user();
		$group = $user->chat_groups()->where('chat_groups.id', $id);

		if($group->exists() == false){
			die();
		}
	
		$chat_group_user = ChatGroupUser::where('group_id',$id)
		                                ->where('user_id',$user->id);
		$chat_group_user->delete();
		
		//Infrom other user for left group
		$chat_message = new GroupChatMessage();
		$chat_message->group_id = $id;
		$chat_message->sender_id = $user->id;
		$chat_message->message = "<b style='color:#EA2027'>".$user->name.' '._lang('left the group').'</b>';
		$chat_message->company_id = company_id();
		$chat_message->save();
		
		$chat_message->attachment = false;
		$chat_message->sender = $user->name;
		$chat_message->group_members = $chat_message->group->group_members->pluck('id');
		
		DB::commit();
		
		$this->pusher->trigger('group-channel', 'group-message-event', $chat_message);

		return response()->json(['result'=>true,'message'=>_lang('You have sucessfuly left the group'),'group_id'=>$id]);	
	
	}
	
	
	public function notification_count(){
		return unread_message_count();
		die();
	}
	
	
}
