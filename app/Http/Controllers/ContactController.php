<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use Validator;
use Auth;
use App\User;
use App\Invoice;
use App\Quotation;
use App\Transaction;
use Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Notifications\ContactAccount as ContactAccountNotification;
use App\Mail\GeneralMail;
use App\Utilities\Overrider;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;
use DB;

class ContactController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));
    	
        $this->middleware(function ($request, $next) {
            if( has_membership_system() == 'enabled' ){
                if( ! has_feature( 'contacts_limit' ) ){
                    return redirect('membership/extend')->with('message',_lang('Your Current package not support this feature. You can upgrade your package !'));
                }

                //If request is create/store
                $route_name = \Request::route()->getName();
                if( $route_name == 'contacts.store'){
                   if( ! has_feature_limit( 'contacts_limit' ) ){
                      if( ! $request->ajax()){
                          return redirect('membership/extend')->with('message', _lang('Your have already reached your usages limit. You can upgrade your package !'));
                      }else{
                          return response()->json(['result'=>'error','message'=> _lang('Your have already reached your usages limit. You can upgrade your package !') ]);
                      }
                   }
                }
            }

            return $next($request);
        });
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.accounting.contacts.contact.list');
	}
	

	public function get_table_data(){
		
		$currency = currency();

		$contacts = Contact::with("group")->select('contacts.*')
										  ->where("contacts.company_id",company_id())
										  ->orderBy("contacts.id","desc");

		return Datatables::eloquent($contacts)

						->editColumn('contact_image', function ($contact) {
							return '<img class="thumb-sm rounded-btn-xs mr-2" src="'.asset('public/uploads/contacts/'.$contact->contact_image) .'">';
						})

						->editColumn('contact_name', function ($contact) {
							return '<a href="'.action('ContactController@show', $contact['id']).'">'.$contact->contact_name.'</a';
						})

						->addColumn('action', function ($contact) {
								return '<form action="'.action('ContactController@destroy', $contact['id']).'" class="text-center" method="post">'
								.'<a href="'.action('ContactController@show', $contact['id']).'" class="btn btn-primary btn-xs"><i class="ti-eye"></i></a>&nbsp;'
								.'<a href="'.action('ContactController@edit', $contact['id']).'" class="btn btn-warning btn-xs"><i class="ti-pencil"></i></a>&nbsp;'
								.csrf_field()
								.'<input name="_method" type="hidden" value="DELETE">'
								.'<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i></button>'
								.'</form>';
						})
						->setRowId(function ($contact) {
							return "row_".$contact->id;
						})
						->rawColumns(['action', 'contact_image', 'contact_name'])
						->make(true);							    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.contacts.contact.create');
		}else{
           return view('backend.accounting.contacts.contact.modal.create');
		}
    }

   
    public function import(Request $request)
    {		
        if($request->isMethod('get')){
			return view('backend.accounting.contacts.contact.import');
        }else{
        	@ini_set('max_execution_time', 0);
	        @set_time_limit(0);

	        $validator = Validator::make($request->all(), [
				'file' => 'required|mimes:xlsx',
			]);
			
			if ($validator->fails()) {
				if($request->ajax()){ 
				    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
				}else{
					return redirect('contacts/import')->withErrors($validator)
													  ->withInput();
				}			
	        }
				
	        //Import Contacts
	        //$file_type = $request->file('file')->getClientOriginalExtension();
            $new_rows = 0;

	        DB::beginTransaction();
	        
	        $previous_rows = Contact::where('company_id',company_id())->count();
            
			$data = array();
            $data['group_id'] = $request->group_id;
	        $import = Excel::import(new ContactsImport($data), request()->file('file'));

	        $current_rows = Contact::where('company_id',company_id())->count();

            $new_rows = $current_rows - $previous_rows;

	        DB::commit();

        	return back()->with('success',$new_rows.' '._lang('Rows Imported Sucessfully'));
        }			
        
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {	
		$validator = Validator::make($request->all(), [
			'profile_type'  => 'required|max:20',
			'company_name'  => 'nullable|max:50',
			'contact_name'  => 'required|max:50',
			'contact_email' => [
                'required',
                'email',
                Rule::unique('contacts')->where('company_id',company_id()),
            ],
			'contact_phone' => 'nullable|max:20',
			'country'       => 'nullable|max:50',
			'currency' 		=> 'required|max:3',
			'city' 			=> 'nullable|max:50',
			'state' 		=> 'nullable|max:50',
			'zip' 			=> 'nullable|max:20',
			'contact_image' => 'nullable|image||max:5120',
			'group_id' 		=> 'required',
			//'name' => 'required_if:client_login,on|max:191', //User Login Attribute
			//'email' => 'required_if:client_login,on|email|unique:users|max:191', //User Login Attribute
			//'password' => 'required_if:client_login,on|max:20|min:6|confirmed', //User Login Attribute
			//'status' => 'required_if:client_login,on', //User Login Attribute
		],[
		    'group_id.required' => 'The group field is required.' 
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('contacts/create')
							->withErrors($validator)
							->withInput();
			}			
		}
		
		$contact_image ="avatar.png";		
	    if($request->hasfile('contact_image'))
		{
			 $file = $request->file('contact_image');
			 $contact_image = "contact_image".time().'.'.$file->getClientOriginalExtension();
			 $file->move(public_path()."/uploads/contacts/", $contact_image);
		}

		
		DB::beginTransaction();

		//Check client has already an account
		$other = User::where('email',$request->contact_email)
		             ->where('user_type','!=','client')->first();

        if( $other ){
        	if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>'Sorry, This email already registered with an company admin or staff !']);
			}else{
				return redirect('contacts/create')->with('error', _lang('Sorry, This email already registered with an company admin or staff !'))->withInput();
			}	
        }

		$client = User::where('email',$request->contact_email)
		              ->where('user_type','client')->first();
		

        $contact = new Contact();
	    $contact->profile_type = $request->input('profile_type');
		$contact->company_name = $request->input('company_name');
		$contact->contact_name = $request->input('contact_name');
		$contact->contact_email = $request->input('contact_email');
		$contact->vat_id = $request->input('vat_id');
		$contact->reg_no = $request->input('reg_no');
		$contact->contact_phone = $request->input('contact_phone');
		$contact->country = $request->input('country');
		$contact->currency = $request->input('currency');
		$contact->city = $request->input('city');
		$contact->state = $request->input('state');
		$contact->zip = $request->input('zip');
		$contact->address = $request->input('address');
		$contact->facebook = $request->input('facebook');
		$contact->twitter = $request->input('twitter');
		$contact->linkedin = $request->input('linkedin');
		$contact->remarks = $request->input('remarks');
		if($client){
			$contact->user_id = $client->id;
		}
		$contact->group_id = $request->input('group_id');
		$contact->company_id = company_id();
		$contact->contact_image = $contact_image;
	
        $contact->save();
		
		//Update Package limit
		update_package_limit('contacts_limit');
		
		DB::commit();
		

		if(! $request->ajax()){
           return redirect('contacts/create')->with('success', _lang('New client added sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('New client added sucessfully'),'data'=>$contact]);
		}
        
   }
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
    	$company_id = company_id();
    	$data = array();

        $data['contact'] = Contact::where("id",$id)
						  		  ->where("company_id", $company_id)->first();

		$data['invoices'] = Invoice::where('client_id',$id)
								   ->where("company_id", $company_id)
								   ->get();
		
		$data['quotations'] = Quotation::where('related_id',$id)
				                       ->where('related_to','contacts')
									   ->where("company_id", $company_id)->get();
		
		$data['transactions'] = Transaction::where('payer_payee_id',$id)
							       		   ->where("company_id", $company_id)->get();


		//Summary Data
		$data['total_project'] = DB::table('projects')->where('client_id',$id)->count();

		$data['invoice_value'] = DB::table('invoices')
									->where('client_id', $id)
									->selectRaw('sum(grand_total) as grand_total, sum(paid) as paid')
									->first();	
									
		$data['invoice_due_amount'] = DB::table('invoices')
									    ->selectRaw('sum(grand_total) as grand_total, sum(paid) as paid')
										->whereRaw("(Status = 'Unpaid' or Status = 'Partially_Paid')")
										->where('client_id', $id)
										->first();								
		
		if(! $request->ajax()){
		    return view('backend.accounting.contacts.contact.view', $data);
		}else{
			return view('backend.accounting.contacts.contact.modal.view', $data);
		} 
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
		$contact = Contact::where("id",$id)
		                  ->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.contacts.contact.edit',compact('contact','id'));
		}else{
           return view('backend.accounting.contacts.contact.modal.edit',compact('contact','id'));
		}  
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    	DB::beginTransaction();

		$contact = Contact::where("id",$id)->where("company_id",company_id())->first();
		
		$validator = Validator::make($request->all(), [
			'profile_type' => 'required|max:20',
			'company_name' => 'nullable|max:50',
			'contact_name' => 'required|max:50',
			'contact_email' => [
                'required',
                'email',
                Rule::unique('contacts')->where('company_id',company_id())->ignore($contact->id),
            ],
			'contact_phone' => 'nullable|max:20',
			'country' => 'nullable|max:50',
			'currency' => 'required|max:3',
			'city' => 'nullable|max:50',
			'state' => 'nullable|max:50',
			'zip' => 'nullable|max:20',
			'contact_image' => 'nullable|image||max:5120',
			'group_id' => 'required',
			
			//'name' => 'required_if:client_login,on|max:191', //User Login Attribute
			//'email' => [
            //    'required_if:client_login,on',
            //    Rule::unique('users')->ignore($contact->user_id),
            //], //User Login Attribute
			//'password' => 'nullable|max:20|min:6|confirmed', //User Login Attribute
			//'status' => 'required_if:client_login,on', //User Login Attribute
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('contacts.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
		
		if($request->hasfile('contact_image'))
		{
			$file = $request->file('contact_image');
			$contact_image = "contact_image".time().'.'.$file->getClientOriginalExtension();
			$file->move(public_path()."/uploads/contacts/", $contact_image);
		}

		//Check client has already an account
		$other = User::where('email',$request->contact_email)
		             ->where('user_type','!=','client')->first();

        if( $other ){
        	if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>'Sorry, This email already registered with an company or staff !']);
			}else{
				return back()->with('error', _lang('Sorry, This email already registered with an company or staff !'))->withInput();
			}	
        }

		$client = User::where('email',$request->contact_email)
		              ->where('user_type','client')->first();
        
		$contact->profile_type = $request->input('profile_type');
		$contact->company_name = $request->input('company_name');
		$contact->contact_name = $request->input('contact_name');
		$contact->contact_email = $request->input('contact_email');
		$contact->contact_phone = $request->input('contact_phone');
		$contact->vat_id = $request->input('vat_id');
		$contact->reg_no = $request->input('reg_no');
		$contact->country = $request->input('country');
		$contact->currency = $request->input('currency');
		$contact->city = $request->input('city');
		$contact->state = $request->input('state');
		$contact->zip = $request->input('zip');
		$contact->address = $request->input('address');
		$contact->facebook = $request->input('facebook');
		$contact->twitter = $request->input('twitter');
		$contact->linkedin = $request->input('linkedin');
		$contact->remarks = $request->input('remarks');
		$contact->group_id = $request->input('group_id');
		if($client){
			$contact->user_id = $client->id;
		}
		$contact->company_id = company_id();
		if($request->hasfile('contact_image')){
			$contact->contact_image = $contact_image;
		}
	
        $contact->save();

        DB::commit();

		if(! $request->ajax()){
           return redirect('contacts')->with('success', _lang('Client information updated sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Client information updated sucessfully'),'data'=>$contact]);
		}
	    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		DB::beginTransaction();
		
        $contact = Contact::where("id",$id)
		                  ->where("company_id",company_id())
						  ->first();
						  
		/*$user = User::find($contact->user_id);
		if($user){
			$user->delete();
		}*/	

        $contact->delete();
		
		DB::commit();
		
		
        return redirect('contacts')->with('success',_lang('Information has been deleted sucessfully'));
    }
	
	
	public function get_client_info( $id = '' ){
		$contact = Contact::where("id",$id)
						  ->where("company_id",company_id())->first();
		echo json_encode($contact);				  
		
	}
	
	
	public function send_email(Request $request, $id)
    {
		@ini_set('max_execution_time', 0);
	    @set_time_limit(0);
	    Overrider::load("Settings");
		
		$validator = Validator::make($request->all(), [
			'email_subject' => 'required',
			'email_message' => 'required',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return back()->withErrors($validator)
							 ->withInput();
			}			
		}
	   
        $contact = Contact::where("id",$id)
		                  ->where("company_id",company_id())->first();
        
		//Send email
		$subject = $request->input("email_subject");
		$message = $request->input("email_message");
		
		$mail  = new \stdClass();
		$mail->subject = $subject;
		$mail->body = $message;
		
		try{
			Mail::to($contact->contact_email)->send(new GeneralMail($mail));
		}catch (\Exception $e) {
			if(! $request->ajax()){
			   return back()->with('error', _lang('Sorry, Error Occured !'));
			}else{
			   return response()->json(['result'=>'error','message'=>_lang('Sorry, Error Occured !')]);
			}
		}
		
        if(! $request->ajax()){
           return back()->with('success', _lang('Email Send Sucessfully'));
        }else{
		   return response()->json(['result'=>'success', 'action'=>'update', 'message'=>_lang('Email Send Sucessfully'),'data'=>$contact]);
		}
    }
}
