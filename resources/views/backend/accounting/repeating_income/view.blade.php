@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
	<div class="card">
	<span class="d-none panel-title">{{ _lang('View Income') }}</span>

	@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	

	<div class="card-body">
	  <table class="table table-bordered">
		<tr><td>{{ _lang('Trans Date') }}</td><td>{{ date($date_format, strtotime($transaction->trans_date)) }}</td></tr>
		<tr><td>{{ _lang('Account') }}</td><td>{{ $transaction->account->account_title }}</td></tr>
		<tr><td>{{ _lang('Income Type') }}</td><td>{{ $transaction->income_type->name }}</td></tr>
		<tr><td>{{ _lang('Amount') }}</td><td>{{ decimalPlace($transaction->amount, currency($transaction->account->account_currency)) }}</td></tr>
		<tr><td>{{ _lang('Base Amount') }}</td><td>{{ decimalPlace($transaction->base_amount, currency()) }}</td></tr>
		<tr><td>{{ _lang('Payer') }}</td><td>{{ isset($transaction->payer->contact_name) ? $transaction->payer->contact_name : '' }}</td></tr>
		<tr><td>{{ _lang('Payment Method') }}</td><td>{{ $transaction->payment_method->name }}</td></tr>
		<tr><td>{{ _lang('Reference') }}</td><td>{{ $transaction->reference }}</td></tr>
		<tr><td>{{ _lang('Note') }}</td><td>{{ $transaction->note }}</td></tr>
	  </table>
	</div>
  </div>
 </div>
</div>
@endsection


