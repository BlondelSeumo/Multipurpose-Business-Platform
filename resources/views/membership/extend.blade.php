@extends('layouts.login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-signin my-5">
                @if (\Session::has('message'))
				  <div class="alert alert-danger text-center">
					<b>{{ \Session::get('message') }}</b>
				  </div>
				@endif	
				
				<div class="card-header text-center">
				  {{ _lang('Membership Payment') }}
				</div>

                <div class="card-body" id="extend_membership">
                    <form method="POST" class="form-signup" action="{{ url('membership/pay') }}">
                        @csrf

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Package') }}</label>	
								<select id="package" class="form-control" name="package" required>
									<option value="">{{ _lang('Select Package') }}</option>
									{{ create_option('packages', 'id', 'package_name', $user->company->package_id) }}
								</select>  
							</div>									
						</div>
						
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Package Type') }}</label>	
								<select class="form-control" name="package_type" required>
									<option value="">{{ _lang('Select Package Type') }}</option>
									<option value="monthly" {{ $user->company->package_type == 'monthly' ? 'selected' : '' }}>{{ _lang('Monthly Pack') }}</option>
									<option value="yearly" {{ $user->company->package_type == 'yearly' ? 'selected' : '' }}>{{ _lang('Yearly Pack') }}</option> 
								</select>  
							</div>									
						</div>
					
						<div class="col-md-12">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Payment Gateway') }}</label>						
							<select class="form-control" name="gateway" id="gateway" required>
								@if (get_option('paypal_active') == 'Yes')
									<option value="PayPal">{{ _lang('PayPal') }}</option>
								@endif
								@if (get_option('stripe_active') == 'Yes')
									<option value="Stripe">{{ _lang('Stripe') }}</option>
							    @endif
							    @if (get_option('razorpay_active') == 'Yes')
									<option value="Razorpay">{{ _lang('Razorpay') }}</option>
							    @endif
							    @if (get_option('paystack_active') == 'Yes')
									<option value="Paystack">{{ _lang('Paystack') }}</option>
							    @endif
							</select>
						  </div>
						</div>
				
						<div class="form-group">
						  <div class="col-md-12">
							<button type="submit" class="btn btn-primary btn-block">{{ _lang('Process') }}</button>
						  </div>
						</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
