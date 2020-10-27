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

					<h5 class="text-center">{{ _lang('Payable Amount') }} : {{ g_decimal_place($amount/100, currency('INR')) }}</h5>
					
					<form action="{{ url('membership/razorpay_payment/'.$payment_id) }}" method="POST">
						{{ csrf_field() }}
						<script
							src="https://checkout.razorpay.com/v1/checkout.js"
							data-key="{{ get_option('razorpay_key_id') }}"
							data-amount="{{ $amount }}"
							data-currency="INR"
							data-name="{{ _lang('Extend Membership') }}"
							data-image="{{ get_logo() }}"
							data-description="{{ $title }}"
							data-prefill.name="{{ Auth::user()->name }}"
							data-prefill.email="{{ Auth::user()->email }}"
							data-prefill.contact=""
							data-notes.shopping_order_id="{{ $payment_id }}"
							data-order_id="{{ $order_id }}">
						</script>
					</form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection