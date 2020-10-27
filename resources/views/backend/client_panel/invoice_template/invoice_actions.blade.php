
@if($invoice->company->online_payment == 'Yes' && $invoice->status != 'Paid')
	<div class="dropdown float-sm-right">
	  <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<i class="fas fa-credit-card"></i> {{ _lang('Make Payment') }}
	  </button>
	  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		@if(get_company_field($invoice->company_id,'paypal_active') == 'yes')
			<a class="dropdown-item" href="{{ url('client/invoice_payment/'.md5($invoice->id).'/paypal') }}">{{ _lang('Via PayPal') }}</a>
		@endif
		
		@if(get_company_field($invoice->company_id,'stripe_active') == 'yes')
			<a class="dropdown-item" href="{{ url('client/invoice_payment/'.md5($invoice->id).'/stripe') }}">{{ _lang('Via Stripe') }}</a>
		@endif
		
		@if(get_company_field($invoice->company_id,'razorpay_active') == 'yes')
			<a class="dropdown-item" href="{{ url('client/invoice_payment/'.md5($invoice->id).'/razorpay') }}">{{ _lang('Via Razorpay') }}</a>
		@endif

		@if(get_company_field($invoice->company_id,'paystack_active') == 'yes')
			<a class="dropdown-item" href="{{ url('client/invoice_payment/'.md5($invoice->id).'/paystack') }}">{{ _lang('Via Paystack') }}</a>
		@endif
	  </div>
	</div>
	
@endif