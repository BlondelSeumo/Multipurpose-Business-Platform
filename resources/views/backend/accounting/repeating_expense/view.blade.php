@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-md-12">
	<div class="panel panel-default">
	<div class="panel-heading">{{ _lang('View Repeating Expense') }}</div>

	@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	

	<div class="panel-body">
	  <table class="table table-bordered">
		<tr><td>{{ _lang('Trans Date') }}</td><td>{{ date($date_format, strtotime($transaction->trans_date)) }}</td></tr>
		<tr><td>{{ _lang('Account') }}</td><td>{{ $transaction->account->account_title }}</td></tr>
		<tr><td>{{ _lang('Expense Type') }}</td><td>{{ $transaction->expense_type->name }}</td></tr>
		<tr><td>{{ _lang('Amount') }}</td><td>{{ decimalPlace($transaction->amount, currency($transaction->account->account_currency)) }}</td></tr>
	    <tr><td>{{ _lang('Base Amount') }}</td><td>{{ decimalPlace($transaction->base_amount, currency()) }}</td></tr>
		<tr><td>{{ _lang('Payee') }}</td><td>{{ isset($transaction->payee->contact_name) ? $transaction->payee->contact_name : '' }}</td></tr>
		<tr><td>{{ _lang('Payment Method') }}</td><td>{{ $transaction->payment_method->name }}</td></tr>
		<tr><td>{{ _lang('Reference') }}</td><td>{{ $transaction->reference }}</td></tr>
		<tr><td>{{ _lang('Note') }}</td><td>{{ $transaction->note }}</td></tr>
	  </table>
	</div>
  </div>
 </div>
</div>
@endsection


