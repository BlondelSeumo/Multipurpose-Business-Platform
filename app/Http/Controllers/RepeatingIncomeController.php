<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RepeatTransaction;
use App\Transaction;
use Validator;
use Illuminate\Validation\Rule;
use DataTables;
use DateTime;
use DB;

class RepeatingIncomeController extends Controller
{
	
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	date_default_timezone_set(get_company_option('timezone', get_option('timezone','Asia/Dhaka')));	

        $this->middleware(function ($request, $next) {
            if( has_membership_system() == 'enabled' ){
                if( ! has_feature( 'recurring_transaction' ) ){
                    if( ! $request->ajax()){
						return redirect('membership/extend')->with('message', _lang('Your Current package not support this feature. You can upgrade your package !'));
                    }else{
						return response()->json(['result'=>'error','message'=>_lang('Sorry, This feature is not available in your current subscription !')]);
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
		return view('backend.accounting.repeating_income.list');
	}	
	

    public function get_table_data(){
		
		$currency = currency();

		$transactions = RepeatTransaction::with("account")
		                                 ->with("income_type")
										 ->with("payer")
										 ->select('repeating_transactions.*')
										 ->where("repeating_transactions.company_id",company_id())
										 ->where("repeating_transactions.type","income")
										 ->orderBy("repeating_transactions.id","desc");

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
						->editColumn('payer.contact_name', function ($trans) {
							return isset($trans->payer->contact_name) ? $trans->payer->contact_name : '';
						})
						->editColumn('status', function ($trans) {
                            return $trans->status == 0 ? '<span class="badge badge-danger">'._lang('Pending').'</span>' : '<span class="badge badge-success">'._lang('Completed').'</span>';
						})
						->addColumn('action', function ($trans) {
							return '<form action="'.action('RepeatingIncomeController@destroy', $trans['id']).'" class="text-center" method="post">'
							.'<a href="'.action('RepeatingIncomeController@edit', $trans['id']).'" data-title="'._lang('Update Income') .'" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>&nbsp;'
							.'<a href="'.action('RepeatingIncomeController@show', $trans['id']).'" data-title="'._lang('View Income') .'" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>&nbsp;'
							.csrf_field()
							.'<input name="_method" type="hidden" value="DELETE">'
							.'<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i></button>'
							.'</form>';
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
		   return view('backend.accounting.repeating_income.create');
		}else{
           return view('backend.accounting.repeating_income.modal.create');
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
	    @ini_set('max_execution_time', 0);
		@set_time_limit(0);
		
		$validator = Validator::make($request->all(), [
			'trans_date' => 'required',
			'account_id' => 'required',
			'chart_id' => 'required',
			'amount' => 'required|numeric',
			'payment_method_id' => 'required',
			'rotation' => 'required',
			'num_of_rotation' => 'required|integer|min:1|max:100',
			'reference' => 'nullable|max:50',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('income/create')
							->withErrors($validator)
							->withInput();
			}			
		}
			

		$date = $request->input('trans_date');
		$increment = $request->rotation;
		$loop      = $request->num_of_rotation;
		
		DB::beginTransaction();
		
		for ($i = 0; $i < $loop; $i++) {	
			$transaction= new RepeatTransaction();
			$transaction->trans_date = $date;
			$transaction->account_id = $request->input('account_id');
			$transaction->chart_id = $request->input('chart_id');
			$transaction->type = 'income';
			$transaction->dr_cr = 'cr';
			$transaction->amount = $request->input('amount');
			$transaction->base_amount = convert_currency($transaction->account->account_currency, base_currency(), $transaction->amount);
			$transaction->payer_payee_id = $request->input('payer_payee_id');
			$transaction->payment_method_id = $request->input('payment_method_id');
			$transaction->reference = $request->input('reference');
			$transaction->note = $request->input('note');
			$transaction->company_id = company_id();
			$transaction->save();	
			
			$date = date('Y-m-d', strtotime($date . ' + ' . $increment));
		
			$d = new DateTime( $request->input('trans_date') );
			
			if($d->format('d')=='31'){
				$dd = new DateTime( $date );
				if( (int)$dd->format('d') < 31 &&  $dd->format('m') != '03'){
					$temp_date = new DateTime( date('Y-m-d', strtotime($date . ' - 1 day') ));								
					$temp_date->modify("last day of this month");
					$date = $temp_date->format( 'Y-m-d' );
				}else if((int)$dd->format('d') == 28 && $dd->format('m') == '03'){
					$dd->modify("last day of this month");
					$date = $dd->format("Y-m-d");
				}else if((int)$dd->format('d') < 31 && $dd->format('m') == '03'){
					$dd->modify("last day of previous month");
					$date = $dd->format("Y-m-d");
				}
			}else if($d->format('d')=='30'){
				$dd = new DateTime( $date );
				if( (int)$dd->format('d') < 30 &&  $dd->format('m') != '03'){
					$temp_date = new DateTime( date('Y-m-d', strtotime($date . ' - 5 day') ));								
					$temp_date->modify("last day of this month");
					$date = $temp_date->format( 'Y-m' )."-30";
				}else if((int)$dd->format('d') == 28 && $dd->format('m') == '03'){
					$dd->modify("last day of this month");
					$date = $dd->format("Y-m")."-30";
				}else if((int)$dd->format('d') < 30 && $dd->format('m') == '03'){
					$dd->modify("last day of previous month");
					$date = $dd->format("Y-m-d");
				}
			}
			//echo $data['date']."<br>";
		}
		
		DB::commit();

		if(! $request->ajax()){
           return redirect('repeating_income')->with('success', _lang('Saved Sucessfully'));
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
        $transaction = RepeatTransaction::where("id",$id)
								  ->where("company_id",company_id())->first();
		if(! $request->ajax()){
		    return view('backend.accounting.repeating_income.view',compact('transaction','id'));
		}else{
			return view('backend.accounting.repeating_income.modal.view',compact('transaction','id'));
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
        $transaction = RepeatTransaction::where("id",$id)
								  ->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.repeating_income.edit',compact('transaction','id'));
		}else{
           return view('backend.accounting.repeating_income.modal.edit',compact('transaction','id'));
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
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('repeating_income.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
		
		DB::beginTransaction();
		
		$transaction = RepeatTransaction::where("id",$id)->where("company_id",company_id())->first();
		$previous_amount = $transaction->amount;
		$transaction->trans_date = $request->input('trans_date');
		$transaction->account_id = $request->input('account_id');
		$transaction->chart_id = $request->input('chart_id');
		$transaction->type = 'income';
		$transaction->dr_cr = 'cr';
		$transaction->amount = $request->input('amount');
		if(($previous_amount != $transaction->amount) || $transaction->base_amount == ''){
			$transaction->base_amount = convert_currency($transaction->account->account_currency, base_currency(), $transaction->amount);
		}
		$transaction->payer_payee_id = $request->input('payer_payee_id');
		$transaction->payment_method_id = $request->input('payment_method_id');
		$transaction->reference = $request->input('reference');
		$transaction->note = $request->input('note');
		$transaction->status = $request->input('status');
		$transaction->company_id = company_id();
		
        $transaction->save();
		
		if($transaction->status == 1 ){
			$trans= new Transaction();
			$trans->trans_date = $transaction->trans_date;
			$trans->account_id = $transaction->account_id;
			$trans->chart_id = $transaction->chart_id;
			$trans->type = 'income';
			$trans->dr_cr = 'cr';
			$trans->amount = $transaction->amount;
			$trans->base_amount = $transaction->base_amount;
			$trans->payer_payee_id = $transaction->payer_payee_id;
			$trans->payment_method_id = $transaction->payment_method_id;
			$trans->reference = $transaction->reference;
			$trans->note = $transaction->note;
			$trans->company_id = $transaction->company_id;
			$trans->save();
			
			$transaction->trans_id = $trans->id;
			$transaction->save();
			
		}else if( $transaction->status == 0 && $transaction->trans_id != "" ){
			$tran = Transaction::find($transaction->trans_id);
			$tran->delete();
			
			$transaction->trans_id = NULL;
			$transaction->save();
		}
	    
		//Set Related Data	
	    $transaction->trans_date = date('d M, Y',strtotime($transaction->trans_date));
	    $transaction->amount = currency()." ".decimalPlace($transaction->amount);
		$transaction->account_id = $transaction->account->account_title;
	    $transaction->chart_id = $transaction->income_type->name;
	    $transaction->payer_payee_id = isset($transaction->payer->contact_name) ? $transaction->payer->contact_name : '';
	    $transaction->payment_method_id = $transaction->payment_method->name;
        $transaction->status = $transaction->status == 0 ? '<span class="badge badge-danger">'._lang('Pending').'</span>' : '<span class="badge badge-success">'._lang('Completed').'</span>';
		
		DB::commit();
		
		if(! $request->ajax()){
           return redirect('repeating_income')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$transaction]);
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
		
		$transaction = RepeatTransaction::where("id",$id)->where("company_id",company_id());
        
		$tran = Transaction::find($transaction->trans_id);
		if($tran){
			$tran->delete();
		}
		
		$transaction->delete();
		
		DB::commit();
		
        return redirect('repeating_income')->with('success',_lang('Removed Sucessfully'));
    }
}
