<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use Validator;
use Illuminate\Validation\Rule;
use DB;

class TransferController extends Controller
{

    public function __construct()
    {
		date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));	
	}
	
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.transfer.create');
		}else{
           return view('backend.accounting.transfer.modal.create');
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
			'account_from' => 'required',
			'account_to' => 'required|different:account_from',
			'amount' => 'required|numeric',
			'payment_method_id' => 'required',
			'reference' => 'nullable|max:50',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('transfer/create')
							->withErrors($validator)
							->withInput();
			}			
		}
		
        DB::beginTransaction();		
        //Add Debit Transaction
        $transaction1= new Transaction();
	    $transaction1->trans_date = $request->input('trans_date');
		$transaction1->account_id = $request->input('account_from');
		$transaction1->chart_id = 0;
		$transaction1->type = 'transfer';
		$transaction1->dr_cr = 'dr';
		$transaction1->amount = $request->input('amount');
		$transaction1->base_amount = convert_currency($transaction1->account->account_currency, base_currency(), $transaction1->amount);;
		$transaction1->payment_method_id = $request->input('payment_method_id');
		$transaction1->reference = $request->input('reference');
		$transaction1->note = $request->input('note');
		$transaction1->company_id = company_id();
		
        $transaction1->save();
		
		//Add Credit Transaction
        $transaction2 = new Transaction();
	    $transaction2->trans_date = $request->input('trans_date');
		$transaction2->account_id = $request->input('account_to');
		$transaction2->chart_id = 0;
		$transaction2->type = 'transfer';
		$transaction2->dr_cr = 'cr';
		//$transaction2->amount = $request->input('amount');
		$transaction2->amount = convert_currency($transaction1->account->account_currency, $transaction2->account->account_currency, $transaction1->amount);
		$transaction2->base_amount = convert_currency($transaction2->account->account_currency, base_currency(), $transaction2->amount);
		$transaction2->payment_method_id = $request->input('payment_method_id');
		$transaction2->reference = $request->input('reference');
		$transaction2->note = $request->input('note');
		$transaction2->company_id = company_id();
		
        $transaction2->save();
		DB::commit();

		if(! $request->ajax()){
           return redirect('transfer/create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$transaction]);
		}
        
   }

}
