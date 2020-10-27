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
                    
					<h5 class="text-center">{{ _lang('Payable Amount') }} : {{ g_decimal_place(convert_currency(get_option('currency','USD'),get_option('paypal_currency','USD'),$amount), currency(get_option('paypal_currency','USD'))) }}</h5>
					<br>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="cmd" value="_xclick">
						<input type="hidden" name="business" value="{{ get_option('paypal_email') }}">
						<input type="hidden" name="item_name" value="{{ $title }}">
						<input type="hidden" name="item_number" value="{{ $payment_id }}">
						<input type="hidden" name="amount" value="{{ convert_currency(get_option('currency','USD'),get_option('paypal_currency','USD'),$amount) }}">
						<input type="hidden" name="no_shipping" value="0">
						<input type="hidden" name="custom" value="{{ $custom }}">
						<input type="hidden" name="no_note" value="1">
						<input type="hidden" name="currency_code" value="{{ get_option('paypal_currency','USD') }}">
						<input type="hidden" name="lc" value="US">
						<input type="hidden" name="bn" value="PP-BuyNowBF">
						
						<input type="hidden" name="return" value="{{ url('membership/paypal/return') }}"/>
						<input type="hidden" name="cancel_return" value="{{ url('membership/paypal/cancel') }}" />
						<input type="hidden" name="notify_url" value="{{ url('membership/paypal_ipn') }}" />
						
						<input type="submit" name="submit" class="btn btn-primary btn-block" value="Pay Now" alt="PayPal - The safer, easier way to pay online.">
					</form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection