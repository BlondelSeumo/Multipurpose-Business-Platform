<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;
use Validator;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
		
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::where("company_id",company_id())
                            ->orderBy("id","desc")->get();
        return view('backend.accounting.supplier.list',compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.supplier.create');
		}else{
           return view('backend.accounting.supplier.modal.create');
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
			'supplier_name' => 'required|max:191',
            'company_name' => 'nullable|max:191',
            'vat_number' => 'nullable|max:191',
			'email' => [
                'required',
                'email',
                Rule::unique('suppliers')->where('company_id',company_id()),
            ],
            'phone' => 'required|max:20',
            'address' => 'nullable|max:191',
            'country' => 'nullable|max:50',
            'city' => 'nullable|max:50',
            'state' => 'nullable|max:50',
            'postal_code' => 'nullable|max:20',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('suppliers/create')
							->withErrors($validator)
							->withInput();
			}			
		}
			
	    
		
        $supplier= new Supplier();
	    $supplier->supplier_name = $request->input('supplier_name');
        $supplier->company_name = $request->input('company_name');
        $supplier->vat_number = $request->input('vat_number');
        $supplier->email = $request->input('email');
        $supplier->phone = $request->input('phone');
        $supplier->address = $request->input('address');
        $supplier->country = $request->input('country');
        $supplier->city = $request->input('city');
        $supplier->state = $request->input('state');
        $supplier->postal_code = $request->input('postal_code');
        $supplier->company_id = company_id();
	
        $supplier->save();
        
		if(! $request->ajax()){
           return redirect('suppliers/create')->with('success', _lang('Saved sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved sucessfully'),'data'=>$supplier]);
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
        $supplier = Supplier::where('id',$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		    return view('backend.accounting.supplier.view',compact('supplier','id'));
		}else{
			return view('backend.accounting.supplier.modal.view',compact('supplier','id'));
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
        $supplier = Supplier::where('id',$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.supplier.edit',compact('supplier','id'));
		}else{
           return view('backend.accounting.supplier.modal.edit',compact('supplier','id'));
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
			'supplier_name' => 'required|max:191',
            'company_name' => 'nullable|max:191',
            'vat_number' => 'nullable|max:191',
			'contact_email' => [
                'required',
                'email',
                Rule::unique('suppliers')->where('company_id',company_id())->ignore($id),
            ],
            'phone' => 'required|max:20',
            'address' => 'nullable|max:191',
            'country' => 'nullable|max:50',
            'city' => 'nullable|max:50',
            'state' => 'nullable|max:50',
            'postal_code' => 'nullable|max:20',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('suppliers.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $supplier = Supplier::where('id',$id)->where("company_id",company_id())->first();
		$supplier->supplier_name = $request->input('supplier_name');
        $supplier->company_name = $request->input('company_name');
        $supplier->vat_number = $request->input('vat_number');
        $supplier->email = $request->input('email');
        $supplier->phone = $request->input('phone');
        $supplier->address = $request->input('address');
        $supplier->country = $request->input('country');
        $supplier->city = $request->input('city');
        $supplier->state = $request->input('state');
        $supplier->postal_code = $request->input('postal_code');
        $supplier->company_id = company_id();
	
        $supplier->save();
		
		if(! $request->ajax()){
           return redirect('suppliers')->with('success', _lang('Updated sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated sucessfully'),'data'=>$supplier]);
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
        $supplier = Supplier::where('id',$id)->where("company_id",company_id());
        $supplier->delete();
        return redirect('suppliers')->with('success',_lang('Deleted sucessfully'));
    }
}
