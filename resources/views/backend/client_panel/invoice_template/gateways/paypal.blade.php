@extends('layouts.public')

@section('content')

<div class="row">
	<div class="col-md-6 offset-md-3">
		<div class="card">
			<h5 class="card-header bg-primary text-white mt-0 panel-title text-center">{{ _lang('Pay Via PayPal') }}</h5>
			
			<div class="card-body">
			    
				@php $base_currency = get_company_field( $invoice->company_id, 'base_currency', 'USD' ); @endphp
				@php $date_format = get_company_field($invoice->company_id, 'date_format','Y-m-d'); @endphp	
				@php $currency = currency($base_currency); @endphp	

				@if($invoice->related_to == 'contacts' && isset($invoice->client))
					@php $client_currency = $invoice->client->currency; @endphp
					@php $client = $invoice->client; @endphp
				@else 
					@php $client_currency = $invoice->project->client->currency; @endphp
					@php $client = $invoice->project->client; @endphp
				@endif
	
		
				<table class="table table-striped">
					<tr>
						<td>{{ _lang('Invoice No') }}</td>
						<td>{{ $invoice->invoice_number }}</td>
					</tr>
					<tr>
						<td>{{ _lang('Invoice Date') }}</td>
						<td>{{ date($date_format, strtotime( $invoice->invoice_date)) }}</td>
					</tr>
					<tr>
						<td>{{ _lang('Due Date') }}</td>
						<td>{{ date($date_format, strtotime( $invoice->due_date)) }}</td>
					</tr>
					<tr>
						<td>{{ _lang('Due Amount') }}</td>
						<td>
							<span>{{ decimalPlace(($invoice->grand_total - $invoice->paid), $currency) }}</span>
							@if($client_currency != $base_currency)
								<br><span>{{ decimalPlace(convert_currency($base_currency, $client_currency, ($invoice->grand_total - $invoice->paid)), currency($client_currency)) }}</span>	
							@endif
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
								<input type="hidden" name="cmd" value="_xclick">
								<input type="hidden" name="business" value="{{ get_company_field($invoice->company_id,'paypal_email') }}">
								<input type="hidden" name="item_name" value="{{ _lang('Invoice Payment') }}">
								<input type="hidden" name="item_number" value="{{ $invoice->invoice_number }}">
								<input type="hidden" name="amount" value="{{ convert_currency($base_currency, get_company_field($invoice->company_id,'paypal_currency'), ($invoice->grand_total - $invoice->paid)) }}">
								<input type="hidden" name="no_shipping" value="0">
								<input type="hidden" name="custom" value="{{ $invoice->id }}">
								<input type="hidden" name="no_note" value="1">
								<input type="hidden" name="currency_code" value="{{ get_company_field($invoice->company_id,'paypal_currency') }}">
								<input type="hidden" name="lc" value="US">
								<input type="hidden" name="bn" value="PP-BuyNowBF">
								
								<input type="hidden" name="return" value="{{ url('client/paypal/return/'.$invoice->id) }}"/>
								<input type="hidden" name="cancel_return" value="{{ url('client/paypal/cancel/'.$invoice->id) }}" />
								<input type="hidden" name="notify_url" value="{{ url('client/paypal_ipn') }}" />
								
								<button type="submit" name="submit" class="btn btn-primary btn-primary btn-block" alt="PayPal - The safer, easier way to pay online."><i class="fab fa-paypal"></i> {{ _lang('Pay Now') }}</button>
							</form> 
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

