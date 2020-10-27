@extends('layouts.public')

@section('content')

<div class="row">
	<div class="col-md-6 offset-md-3">
		<div class="card">
			<h5 class="card-header bg-primary text-white mt-0 panel-title text-center">{{ _lang('Pay Via Stripe') }}</h5>
			
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
							<button id="checkout-button" class="btn btn-primary btn-block">{{ _lang('Pay Via Stripe') }}</button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js-script')
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
  // Create an instance of the Stripe object with your publishable API key
  var stripe = Stripe('{{ get_option('stripe_publishable_key') }}');
  var checkoutButton = document.getElementById('checkout-button');

  checkoutButton.addEventListener('click', function() {
      stripe.redirectToCheckout({
	     sessionId: '{{ $session_id }}'
	  }).then(function (result) {
		  if(result.error){
			  alert(result.error.message);
		  }
	  });
  });
</script>

@endsection

