<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use Validator;
use Illuminate\Validation\Rule;
use DataTables;

class ExpenseController extends Controller
{
	
    public function __construct()
    {
		date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));	
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.accounting.expense.list');
	}
	

	public function get_table_data(){
		
		$currency = currency();

		$transactions = Transaction::with("account")->with("expense_type")
										 ->with("payment_method")
										 ->select('transactions.*')
										 ->where("transactions.company_id",company_id())
										 ->where("transactions.dr_cr","dr")
										 ->orderBy("transactions.id","desc");

		return Datatables::eloquent($transactions)
						->editColumn('trans_date', function ($trans) {
							$date_format = get_company_option('date_format','Y-m-d');
							return date($date_format, strtotime($trans->trans_date));
						})
						->editColumn('amount', function ($trans) use ($currency){
							$acc_currency = currency($trans->account->account_currency);
							if($acc_currency != $currency){
								return "<span class='float-right'>".decimalPlace($trans->amount, currency($trans->account->account_currency))."</span><br>
										<span class='float-right'><b>".decimalPlace($trans->base_amount, $currency)."</b></span>";
							}else{
								return "<span class='float-right'>".decimalPlace($trans->amount, currency($trans->account->account_currency))."</span>";
							}		
						})
						->editColumn('payee.contact_name', function ($trans) {
							return isset($trans->payee->contact_name) ? $trans->payee->contact_name : '';
						})
						->editColumn('expense_type.name', function ($trans) {
							return isset($trans->expense_type->name) ? $trans->expense_type->name : _lang('Transfer');
						})
						->addColumn('action', function ($trans) {
							if(isset($trans->expense_type->name)){
								return '<form action="'.action('ExpenseController@destroy', $trans['id']).'" class="text-center" method="post">'
								.'<a href="'.action('ExpenseController@edit', $trans['id']).'" data-title="'._lang('Update Income') .'" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>&nbsp;'
								.'<a href="'.action('ExpenseController@show', $trans['id']).'" data-title="'._lang('View Income') .'" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>&nbsp;'
								.csrf_field()
								.'<input name="_method" type="hidden" value="DELETE">'
								.'<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i></button>'
								.'</form>';
							}else{
								return '<form action="'.action('ExpenseController@destroy', $trans['id']).'" class="text-center" method="post">'
								.'<a href="#" data-title="'._lang('Update Income') .'" class="btn btn-warning btn-xs ajax-modal" disabled><i class="ti-pencil"></i></a>&nbsp;'
								.'<a href="'.action('ExpenseController@show', $trans['id']).'" data-title="'._lang('View Income') .'" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>&nbsp;'
								.csrf_field()
								.'<input name="_method" type="hidden" value="DELETE">'
								.'<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i></button>'
								.'</form>';
							}
						})
						->setRowId(function ($trans) {
							return "row_".$trans->id;
						})
						->rawColumns(['status','action','amount'])
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
		   return view('backend.accounting.expense.create');
		}else{
           return view('backend.accounting.expense.modal.create');
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
			'trans_date' => 'required',
			'account_id' => 'required',
			'chart_id' => 'required',
			'amount' => 'required|numeric',
			'payment_method_id' => 'required',
			'reference' => 'nullable|max:50',
			'attachment' => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('expense/create')
							->withErrors($validator)
							->withInput();
			}			
		}

		$attachment = "";
        if($request->hasfile('attachment'))
		{
		  $file = $request->file('attachment');
		  $attachment = time().$file->getClientOriginalName();
		  $file->move(public_path()."/uploads/transactions/", $attachment);
		}
			

        $transaction= new Transaction();
	    $transaction->trans_date = $request->input('trans_date');
		$transaction->account_id = $request->input('account_id');
		$transaction->chart_id = $request->input('chart_id');
		$transaction->type = 'expense';
		$transaction->dr_cr = 'dr';
		$transaction->amount = $request->input('amount');
		$transaction->base_amount = convert_currency($transaction->account->account_currency, base_currency(), $transaction->amount);
		
		if($request->input('related_to') == ''){
			$transaction->payer_payee_id = null;
			$transaction->project_id = null;
		}else if($request->input('related_to') == 'contacts'){
			$transaction->payer_payee_id = $request->input('payer_payee_id');
		}else if($request->input('related_to') == 'projects'){
			$transaction->project_id = $request->input('project_id');
		}
		
		$transaction->payment_method_id = $request->input('payment_method_id');
		$transaction->reference = $request->input('reference');
		$transaction->note = $request->input('note');
		$transaction->attachment = $attachment;
		$transaction->company_id = company_id();
		
        $transaction->save();

		//Set Prefix Data	
		$date_format = get_company_option('date_format','Y-m-d');
	    $transaction->trans_date = date("$date_format",strtotime($transaction->trans_date));
	    $transaction->amount = decimalPlace($transaction->amount, currency());
		$transaction->account_id = $transaction->account->account_title;
	    $transaction->chart_id = $transaction->expense_type->name;
	    $transaction->payer_payee_id = isset($transaction->payee->contact_name) ? $transaction->payee->contact_name : '';
	    $transaction->payment_method_id = $transaction->payment_method->name;
        
		if(! $request->ajax()){
           return redirect('expense/create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$transaction]);
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
        $transaction = Transaction::where("id",$id)
								  ->where("company_id",company_id())->first();
		if(! $request->ajax()){
		    return view('backend.accounting.expense.view',compact('transaction','id'));
		}else{
			return view('backend.accounting.expense.modal.view',compact('transaction','id'));
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
        $transaction = Transaction::where("id",$id)
								  ->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.expense.edit',compact('transaction','id'));
		}else{
           return view('backend.accounting.expense.modal.edit',compact('transaction','id'));
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
			'trans_date' => 'required',
			'account_id' => 'required',
			'chart_id' => 'required',
			'amount' => 'required|numeric',
			'payment_method_id' => 'required',
			'reference' => 'nullable|max:50',
			'attachment' => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('expense.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}

		$attachment = "";
        if($request->hasfile('attachment'))
		{
		  $file = $request->file('attachment');
		  $attachment = time().$file->getClientOriginalName();
		  $file->move(public_path()."/uploads/transactions/", $attachment);
		}
		

		$transaction = Transaction::where("id",$id)->where("company_id",company_id())->first();
		$previous_amount = $transaction->amount;
		$transaction->trans_date = $request->input('trans_date');
		$transaction->account_id = $request->input('account_id');
		$transaction->chart_id = $request->input('chart_id');
		$transaction->type = 'expense';
		$transaction->dr_cr = 'dr';
		$transaction->amount = $request->input('amount');
		if(($previous_amount != $transaction->amount) || $transaction->base_amount == ''){
			$transaction->base_amount = convert_currency($transaction->account->account_currency, base_currency(), $transaction->amount);
		}

		if($request->input('related_to') == ''){
			$transaction->payer_payee_id = null;
			$transaction->project_id = null;
		}else if($request->input('related_to') == 'contacts'){
			$transaction->payer_payee_id = $request->input('payer_payee_id');
		}else if($request->input('related_to') == 'projects'){
			$transaction->project_id = $request->input('project_id');
		}

		$transaction->payment_method_id = $request->input('payment_method_id');
		$transaction->reference = $request->input('reference');
		$transaction->note = $request->input('note');
		if($request->hasfile('attachment')){
			$transaction->attachment = $attachment;
		}
		$transaction->company_id = company_id();
		
        $transaction->save();
        
	    //Set Related Data	
	    $date_format = get_company_option('date_format','Y-m-d');
	    $transaction->trans_date = date("$date_format",strtotime($transaction->trans_date));
	    $transaction->amount = decimalPlace($transaction->amount, currency());
		$transaction->account_id = $transaction->account->account_title;
	    $transaction->chart_id = $transaction->expense_type->name;
	    $transaction->payer_payee_id = isset($transaction->payee->contact_name) ? $transaction->payee->contact_name : '';
	    $transaction->payment_method_id = $transaction->payment_method->name;
        
		
		if(! $request->ajax()){
           return redirect('expense')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$transaction]);
		}
	    
    }
	
	
	public function calendar()
    {
        $transactions = Transaction::where("company_id",company_id())
		                            ->where("type","expense")
									->orderBy("id","desc")->get();
        return view('backend.accounting.expense.calendar',compact('transactions'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		$transaction = Transaction::where("id",$id)->where("company_id",company_id());
        $transaction->delete();
        return redirect('expense')->with('success',_lang('Removed Sucessfully'));
    }
}
