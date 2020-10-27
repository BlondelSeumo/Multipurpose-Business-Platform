<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\User;
use Validator;
use Hash;
use Image;

class StaffController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if( has_membership_system() == 'enabled' ){
                if( ! has_feature( 'staff_limit' ) ){
                    return redirect('membership/extend')->with('message',_lang('Your Current package not support this feature. You can upgrade your package !'));
                }

                // If request is create/store
                $route_name = \Request::route()->getName();
                if( $route_name == 'staffs.store'){
                   if( ! has_feature_limit( 'staff_limit' ) ){
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
        $users = User::where("user_type","staff")
                     ->Where("company_id",company_id())
                     ->orderBy("id","desc")->get();
        return view('backend.accounting.staff.list',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.staff.create');
		}else{
           return view('backend.accounting.staff.modal.create');
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
			'name'     => 'required|max:191',
			'email'    => 'required|email|unique:users|max:191',
			'password' => 'required|max:20|min:6|confirmed',
            'status'   => 'required',
			'role_id'  => 'required',
			'profile_picture' => 'nullable|image|max:5120',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('users/create')
							->withErrors($validator)
							->withInput();
			}			
		}
			
        $user = new User();
	    $user->name = $request->input('name');
		$user->email = $request->input('email');
		$user->email_verified_at = date('Y-m-d H:i:s');
		$user->password = Hash::make($request->password);
		$user->user_type = 'staff';
		$user->status = $request->input('status');
		$user->role_id = $request->input('role_id');
	    $user->profile_picture = 'default.png';
        
		if ($request->hasFile('profile_picture')){
           $image = $request->file('profile_picture');
           $file_name = "profile_".time().'.'.$image->getClientOriginalExtension();
           //$image->move(base_path('public/uploads/profile/'),$file_name);
           Image::make($image)->crop(300, 300)->save(base_path('public/uploads/profile/') .$file_name);
		   $user->profile_picture = $file_name;
		}
		$user->company_id = company_id();
        $user->save();
      
	    //Update Package limit
        update_package_limit( 'staff_limit' );
		
		//Set Data
        $user->user_type = ucwords($user->user_type);
		$user->role_id = $user->role->name;
		$user->status = $user->status == 1 ? _lang('Active') : _lang('In-Active');
		
        
		if(! $request->ajax()){
           return redirect('staffs/create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$user]);
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
        $user = User::where("id",$id)
                    ->where("company_id",company_id())
                    ->first();
		if(! $request->ajax()){
		    return view('backend.accounting.staff.view',compact('user','id'));
		}else{
			return view('backend.accounting.staff.modal.view',compact('user','id'));
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
        $user = User::where("id",$id)
                    ->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.staff.edit',compact('user','id'));
		}else{
           return view('backend.accounting.staff.modal.edit',compact('user','id'));
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
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:191',
			'email' => [
                'required',
                Rule::unique('users')->ignore($id),
            ],
            'role_id'  => 'required',
			'password' => 'nullable|max:20|min:6|confirmed',
			'status' => 'required',
			'profile_picture' => 'nullable|image|max:5120',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('users.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        $user = User::where("id",$id)
                    ->where("company_id",company_id())->first();
		$user->name = $request->input('name');
		$user->email = $request->input('email');
		if($request->password){
            $user->password = Hash::make($request->password);
        }
		$user->user_type = 'staff';
		$user->status = $request->input('status');
		$user->role_id = $request->input('role_id');
	    if ($request->hasFile('profile_picture')){
           $image = $request->file('profile_picture');
           $file_name = "profile_".time().'.'.$image->getClientOriginalExtension();
           //$image->move(base_path('public/uploads/profile/'),$file_name);
           Image::make($image)->crop(300, 300)->save(base_path('public/uploads/profile/') .$file_name);
		   $user->profile_picture = $file_name;
		}
		$user->company_id = company_id();
        $user->save();
		
		//Set Data
		$user->user_type = ucwords($user->user_type);
        $user->role_id = $user->role->name;
		$user->status = $user->status == 1 ? _lang('Active') : _lang('In-Active');
		
		if(! $request->ajax()){
           return redirect('staffs')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$user]);
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
        $user = User::where("id",$id)
                    ->where("company_id",company_id());
        $user->delete();
        return redirect('staffs')->with('success',_lang('Removed Sucessfully'));
    }
	
}
