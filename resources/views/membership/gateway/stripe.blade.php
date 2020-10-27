@extends('layouts.login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-signin my-5">			
				<div class="card-header text-center">
				  {{ _lang('Extend Membership') }}
				</div>

                <div class="card-body" id="extend_membership">
					<h5 class="text-center">{{ _lang('Payable Amount') }} : {{ g_decimal_place(convert_currency(get_option('currency','USD'),get_option('stripe_currency','USD'),$amount), currency(get_option('stripe_currency','USD'))) }}</h5>
 					<button id="checkout-button" class="btn btn-primary btn-block">{{ _lang('Pay Via Stripe') }}</button>
                </div>
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