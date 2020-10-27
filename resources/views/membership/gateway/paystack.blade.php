@extends('layouts.login')
<style>
 .stripe-button-el{width: 100% !important;}
</style>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-signin my-5">			
				<div class="card-header text-center">
				  {{ _lang('Extend Membership') }}
				</div>

                <div class="card-body" id="extend_membership">

					<h5 class="text-center">{{ _lang('Payable Amount') }} : {{ g_decimal_place(convert_currency(get_option('currency','USD'),get_option('paystack_currency','USD'), $amount), currency(get_option('paystack_currency','GHS'))) }}</h5>

					<button type="button" class="btn btn-primary btn-block" onclick="payWithPaystack()"> {{ _lang('Pay Now') }}</button>
					
					<script src="https://js.paystack.co/v1/inline.js"></script> 

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-script')
<script type="text/javascript">

function payWithPaystack(e) {
  let handler = PaystackPop.setup({
    key: '{{ get_option('paystack_public_key') }}',
    email: '{{ Auth::user()->email }}',
    amount: {{ round(convert_currency(get_option('currency','USD'), get_option('paystack_currency','GHS'),($amount * 100))) }},
    currency: '{{ get_option('paystack_currency','GHS') }}',
    firstname: '{{ Auth::user()->name }}',
    ref: '{{ $payment_id }}', 
    callback: function(response){
    	window.location = "{{ url('membership/paystack_payment/'.$payment_id) }}/" + response.reference;
    }
  });
  handler.openIframe();
}

</script>
@endsection