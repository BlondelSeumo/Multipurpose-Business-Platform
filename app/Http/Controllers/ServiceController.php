<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;
use App\Item;
use Validator;
use Illuminate\Validation\Rule;
use App\Imports\ServicesImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class ServiceController extends Controller
{
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::where("company_id",company_id())
		            ->where("item_type","service")
                    ->orderBy("id","desc")->get();
        return view('backend.accounting.service.list',compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.service.create');
		}else{
           return view('backend.accounting.service.modal.create');
		}
    }

    /** Excel Import**/
    public function import(Request $request)
    {       
        if($request->isMethod('get')){
            return view('backend.accounting.service.import');
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
                    return redirect('services/import')->withErrors($validator)
                                                      ->withInput();
                }           
            }
                
            $new_rows = 0;

            DB::beginTransaction();
            
            $previous_rows = Item::where('company_id',company_id())->count();

            $import = Excel::import(new ServicesImport, request()->file('file'));

            $current_rows = Item::where('company_id',company_id())->count();

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
			'item_name' => 'required',
            'cost' => 'required|numeric',
            'tax_method' => 'required',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('services/create')
							->withErrors($validator)
							->withInput();
			}			
		}
		
        DB::beginTransaction();	
		
        //Create Item
        $item = new Item();
        $item->item_name = $request->input('item_name');
        $item->item_type = 'service';
        $item->company_id = company_id();
        $item->save();


	    //Create Product
        $service = new Service();
	    $service->item_id = $item->id;
        $service->cost = $request->input('cost');
        $service->tax_method = $request->input('tax_method');
        $service->tax_id = $request->input('tax_id');
        $service->description = $request->input('description');
        
        $service->save();
		
		DB::commit();
        
		if(! $request->ajax()){
           return redirect('services/create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$service]);
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
        $item = Item::where("id",$id)->where("company_id",company_id())->first();

		if(! $request->ajax()){
		    return view('backend.accounting.service.view',compact('item','id'));
		}else{
			return view('backend.accounting.service.modal.view',compact('item','id'));
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
        $item = Item::where("id",$id)->where("company_id",company_id())->first();

		if(! $request->ajax()){
		   return view('backend.accounting.service.edit',compact('item','id'));
		}else{
           return view('backend.accounting.service.modal.edit',compact('item','id'));
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
			'item_name' => 'required',
            'cost' => 'required|numeric',
            'tax_method' => 'required',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('services.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
    
        //Update item
		DB::beginTransaction();
        $item = Item::where("id",$id)->where("company_id",company_id())->first();
        
        if( $item ){

            $item->item_name = $request->input('item_name');
            $item->item_type = 'service';
            $item->company_id = company_id();
            $item->save();

            
            $service = Service::where("item_id",$id)->first();
            $service->item_id = $item->id;
            $service->cost = $request->input('cost');
            $service->tax_method = $request->input('tax_method');
            $service->tax_id = $request->input('tax_id');
            $service->description = $request->input('description');
        
            $service->save();
			DB::commit();
        }else{
            if(! $request->ajax()){
                return redirect('services')->with('error', _lang('Update Failed !'));
             }else{
                return response()->json(['result'=>'error','message'=>_lang('Update Failed !')]);
             }
        }

		
		if(! $request->ajax()){
           return redirect('services')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$service]);
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
        $item = Item::where("id",$id)->where("company_id",company_id());
        $item->delete();

        $service = Service::where("item_id",$id);
        $service->delete();
		DB::commit();
        return redirect('services')->with('success',_lang('Information has been deleted sucessfully'));
    }
}
