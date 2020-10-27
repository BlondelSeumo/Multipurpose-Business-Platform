@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-3">
		  <ul class="nav flex-column nav-tabs settings-tab" role="tablist">
			  <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#company_settings" aria-expanded="true">{{ _lang('Company Settings') }}</a></li>
			  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#system_settings" aria-expanded="true">{{ _lang('System Settings') }}</a></li>
			  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#invoice" aria-expanded="true">{{ _lang('Invoice & Quotation') }}</a></li>
			  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#payment-gateway" aria-expanded="false">{{ _lang('Payment Gateway') }}</a></li>
			  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#logo" aria-expanded="false">{{ _lang('Logo') }}</a></li>
		  </ul>
		</div>
		
		<div class="col-sm-9">
		  <div class="tab-content">
				
			  <div id="company_settings" class="tab-pane active">
				  <div class="card">
				  <span class="d-none panel-title">{{ _lang('Company Settings') }}</span>

				  <div class="card-body">
					  
					  <form method="post" class="validate params-panel" autocomplete="off" action="{{ url('company/general_settings/update') }}" enctype="multipart/form-data">
						{{ csrf_field() }}
						
						<div class="row">
							<div class="col-md-12">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Company Name') }}</label>						
								<input type="text" class="form-control" name="company_name" value="{{ get_company_option('company_name') }}" required>
							  </div>
							</div>					
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Phone') }}</label>						
								<input type="text" class="form-control" name="phone" value="{{ get_company_option('phone') }}">
							  </div>
							</div>
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('VAT ID') }}</label>						
								<input type="text" class="form-control" name="vat_id" value="{{ get_company_option('vat_id') }}">
							  </div>
							</div>					
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Reg No') }}</label>						
								<input type="text" class="form-control" name="reg_no" value="{{ get_company_option('reg_no') }}">
							  </div>
							</div>
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Email') }}</label>						
								<input type="text" class="form-control" name="email" value="{{ get_company_option('email') }}">
							  </div>
							</div>
						
							<div class="col-md-12">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Address') }}</label>						
								<textarea class="form-control" name="address">{{ get_company_option('address') }}</textarea>
							  </div>
							</div>

								
							<div class="col-md-12">
							  <div class="form-group">
								<button type="submit" class="btn btn-primary">{{ _lang('Save Settings') }}</button>
							  </div>
							</div>
						</div>
					  </form>
				  </div>
				  </div>
			  </div>

			  <div id="system_settings" class="tab-pane">
				  <div class="card">
				  <span class="d-none panel-title">{{ _lang('Company Settings') }}</span>

				  <div class="card-body">
				      @if(get_company_option('base_currency') == '')
					  <div class="alert alert-warning">
					    <h5><b><i class="fas fa-info-circle"></i> {{ _lang('You cannot change Base Currency once you created !') }}</b></h5>
					  </div>
					  @endif
					  
					  <form method="post" class="validate params-panel" autocomplete="off" action="{{ url('company/general_settings/update') }}" enctype="multipart/form-data">
						{{ csrf_field() }}
						
						<div class="row">


							@if(get_company_option('base_currency') == '')
								<div class="col-md-6">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Base Currency') }}</label>						
									<select class="form-control select2" name="base_currency" id="base_currency" required>
										<option value="">{{ _lang('Select One') }}</option>
										{{ get_currency_list( ) }}
									</select>
								  </div>
								</div>
							@else
								<div class="col-md-6">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Base Currency') }}</label>						
									<select class="form-control select2 auto-select" data-selected="{{ get_company_option('base_currency') }}" id="base_currency" disabled>
										<option value="">{{ _lang('Select One') }}</option>
										{{ get_currency_list( ) }}
									</select>
								  </div>
								</div>
							@endif
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Currency Position') }}</label>						
								<select class="form-control" name="currency_position" required>
									<option value="left" {{ get_company_option('currency_position') == 'left' ? 'selected' : '' }}>{{ _lang('Left') }}</option>
									<option value="right" {{ get_company_option('currency_position') == 'right' ? 'selected' : '' }}>{{ _lang('Right') }}</option>
								</select>
							  </div>
							</div>
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Backend Direction') }}</label>						
								<select class="form-control auto-select" data-selected="{{ get_company_option('backend_direction','ltr') }}" name="backend_direction" required>
									<option value="ltr">{{ _lang('LTR') }}</option>
									<option value="rtl">{{ _lang('RTL') }}</option>
								</select>
							  </div>
							</div>
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Timezone') }}</label>						
								<select class="form-control select2" name="timezone" required>
								<option value="">{{ _lang('-- Select One --') }}</option>
								{{ create_timezone_option(get_company_option('timezone')) }}
								</select>
							  </div>
							</div>					
							

							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Date Format') }}</label>					
								<select class="form-control auto-select" name="date_format" data-selected="{{ get_company_option('date_format','Y-m-d') }}" required>
									<option value="Y-m-d">{{ date('Y-m-d') }}</option>
									<option value="d-m-Y">{{ date('d-m-Y') }}</option>
									<option value="d/m/Y">{{ date('d/m/Y') }}</option>
									<option value="m-d-Y">{{ date('m-d-Y') }}</option>
									<option value="m.d.Y">{{ date('m.d.Y') }}</option>
									<option value="m/d/Y">{{ date('m/d/Y') }}</option>
									<option value="d.m.Y">{{ date('d.m.Y') }}</option>
									<option value="d/M/Y">{{ date('d/M/Y') }}</option>
									<option value="d/M/Y">{{ date('M/d/Y') }}</option>
									<option value="d M, Y">{{ date('d M, Y') }}</option>
								</select>
							  </div>
							</div>

							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Time Format') }}</label>		
								<select class="form-control auto-select" name="time_format" data-selected="{{ get_company_option('time_format',24) }}" required="">
									<option value="24">{{ _lang('24 Hours') }}</option>
									<option value="12">{{ _lang('12 Hours') }}</option>
								</select>
							  </div>
							</div>
						
								
							<div class="col-md-12">
							  <div class="form-group">
								<button type="submit" class="btn btn-primary">{{ _lang('Save Settings') }}</button>
							  </div>
							</div>
						</div>
					  </form>
				  </div>
				  </div>
			  </div>
			  
			  <div id="invoice" class="tab-pane">
				  <div class="card">
				  <span class="d-none panel-title">{{ _lang('Invoice & Quotation Settings') }}</span>

				  <div class="card-body">
					  <form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('company/general_settings/update') }}" enctype="multipart/form-data">
						{{ csrf_field() }}
						
						<div class="row">
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Invoice Prefix') }}</label>						
								<input type="text" class="form-control" name="invoice_prefix" value="{{ get_company_option('invoice_prefix') }}">
							  </div>
							</div>
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Next Invoice Number') }}</label>						
								<input type="number" class="form-control" name="invoice_starting" min="1" value="{{ get_company_option('invoice_starting',1001) }}" required>
							  </div>
							</div>
							
							<div class="col-md-6">
							  <div class="form-group">
								<a href="{{ route('accounts.create') }}" data-reload="false" data-title="{{ _lang('Create Account') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
								<label class="control-label">{{ _lang('Default Account') }}</label>						
								<select class="form-control select2-ajax" data-value="id" data-display="account_title" data-table="accounts" data-where="1" name="default_account" required>
									<option value="">{{ _lang('Select One') }}</option>
									{{ create_option("accounts","id","account_title",get_company_option('default_account'),array("company_id="=>company_id())) }}
								</select>
								<p>{{ _lang('Use for accepting Online Payment') }}</p>
							  </div>  
							</div>
							
							
							<div class="col-md-6">
							  <div class="form-group">
								<a href="{{ route('chart_of_accounts.create') }}" data-reload="false" data-title="{{ _lang('Add Income/Expense Type') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
								<label class="control-label">{{ _lang('Default Income Category') }}</label>						
								<select class="form-control select2-ajax" data-value="id" data-display="name" data-table="chart_of_accounts" data-where="3" name="default_chart_id" required>
									<option value="">{{ _lang('Select One') }}</option>
									{{ create_option("chart_of_accounts","id","name",get_company_option('default_chart_id'),array("type="=>"income","AND company_id="=>company_id())) }}
								</select>
								<p>{{ _lang('Use for accepting Online Payment') }}</p>
							  </div>
							</div>
							

							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Quotation Prefix') }}</label>						
								<input type="text" class="form-control" name="quotation_prefix" value="{{ get_company_option('quotation_prefix') }}">
							  </div>
							</div>
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Next Quotation Number') }}</label>						
								<input type="number" class="form-control" name="quotation_starting" min="1" value="{{ get_company_option('quotation_starting',1001) }}" required>
							  </div>
							</div>
							
							<div class="col-md-12">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Invoice Footer') }} ( HTML Allowed )</label>						
								<textarea class="form-control summernote" rows="5" name="invoice_footer">{{ get_company_option('invoice_footer') }}</textarea>
							  </div>
							</div>
							
							<div class="col-md-12">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Quotation Footer') }} ( HTML Allowed )</label>						
								<textarea class="form-control summernote" rows="5" name="quotation_footer">{{ get_company_option('quotation_footer') }}</textarea>
							  </div>
							</div>
																
							<div class="col-md-12">
							  <div class="form-group">
								<button type="submit" class="btn btn-primary">{{ _lang('Save Settings') }}</button>
							  </div>
							</div>
						</div>
					  </form>
				  </div>
				  </div>
			  </div>
			  
			  <div id="payment-gateway" class="tab-pane fade">
			     <div class="card">
				    <span class="d-none panel-title">{{ _lang('Payment Gateway') }}</span>
				    <div class="card-body">
					   <form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('company/general_settings/update') }}" enctype="multipart/form-data">				         
							
						    {{ csrf_field() }}
							<h5 class="header-title">{{ _lang('PayPal') }}</h5>
							<div class="params-panel">
								<div class="row">
									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('PayPal Active') }}</label>						
										<select class="form-control" name="paypal_active" required>
										   <option value="no" {{ get_company_option('paypal_active') == 'no' ? 'selected' : '' }}>{{ _lang('No') }}</option>
										   <option value="yes" {{ get_company_option('paypal_active') == 'yes' ? 'selected' : '' }}>{{ _lang('Yes') }}</option>
										</select>
									  </div>
									</div>
									
									
									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('PayPal Currency') }}</label>						
										<select class="form-control select2 auto-select" data-selected="{{ get_company_option('paypal_currency') }}" name="paypal_currency" id="paypal_currency" required>
										    <option value="USD">{{ _lang('U.S. Dollar') }}</option>
											<option value="AUD">{{ _lang('Australian Dollar') }}</option>
											<option value="BRL">{{ _lang('Brazilian Real') }}</option>
											<option value="CAD">{{ _lang('Canadian Dollar') }}</option>
											<option value="CZK">{{ _lang('Czech Koruna') }}</option>
											<option value="DKK">{{ _lang('Danish Krone') }}</option>
											<option value="EUR">{{ _lang('Euro') }}</option>
											<option value="HKD">{{ _lang('Hong Kong Dollar') }}</option>
											<option value="HUF">{{ _lang('Hungarian Forint') }}</option>
											<option value="INR">{{ _lang('Indian Rupee') }}</option>
											<option value="ILS">{{ _lang('Israeli New Sheqel') }}</option>
											<option value="JPY">{{ _lang('Japanese Yen') }}</option>
											<option value="MYR">{{ _lang('Malaysian Ringgit') }}</option>
											<option value="MXN">{{ _lang('Mexican Peso') }}</option>
											<option value="NOK">{{ _lang('Norwegian Krone') }}</option>
											<option value="NZD">{{ _lang('New Zealand Dollar') }}</option>
											<option value="PHP">{{ _lang('Philippine Peso') }}</option>
											<option value="PLN">{{ _lang('Polish Zloty') }}</option>
											<option value="GBP">{{ _lang('Pound Sterling') }}</option>
											<option value="SGD">{{ _lang('Singapore Dollar') }}</option>
											<option value="SEK">{{ _lang('Swedish Krona') }}</option>
											<option value="CHF">{{ _lang('Swiss Franc') }}</option>
											<option value="TWD">{{ _lang('Taiwan New Dollar') }}</option>
											<option value="THB">{{ _lang('Thai Baht') }}</option>
											<option value="TRY">{{ _lang('Turkish Lira') }}</option>
										</select>
									  </div>
									</div>
									
									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('PayPal Email') }}</label>						
										<input type="text" class="form-control" name="paypal_email" value="{{ get_company_option('paypal_email') }}">
									  </div>
									</div>
								</div>
							</div>
							
							<br>
							<h5 class="header-title">{{ _lang('Stripe Configuration') }}</h5>
							<div class="params-panel">								
								<div class="row">
									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Stripe Active') }}</label>						
										<select class="form-control" name="stripe_active" required>
										   <option value="no" {{ get_company_option('stripe_active') == 'no' ? 'selected' : '' }}>{{ _lang('No') }}</option>
										   <option value="yes" {{ get_company_option('stripe_active') == 'yes' ? 'selected' : '' }}>{{ _lang('Yes') }}</option>
										</select>
									  </div>
									</div>
									
									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Stripe Currency') }}</label>						
										<select class="form-control select2 auto-select" data-selected="{{ get_company_option('stripe_currency') }}" name="stripe_currency" id="stripe_currency" required>
										    <option value="USD">{{ _lang('U.S. Dollar') }}</option>
											<option value="AUD">{{ _lang('Australian Dollar') }}</option>
											<option value="BRL">{{ _lang('Brazilian Real') }}</option>
											<option value="CAD">{{ _lang('Canadian Dollar') }}</option>
											<option value="CZK">{{ _lang('Czech Koruna') }}</option>
											<option value="DKK">{{ _lang('Danish Krone') }}</option>
											<option value="EUR">{{ _lang('Euro') }}</option>
											<option value="HKD">{{ _lang('Hong Kong Dollar') }}</option>
											<option value="HUF">{{ _lang('Hungarian Forint') }}</option>
											<option value="INR">{{ _lang('Indian Rupee') }}</option>
											<option value="ILS">{{ _lang('Israeli New Sheqel') }}</option>
											<option value="JPY">{{ _lang('Japanese Yen') }}</option>
											<option value="MYR">{{ _lang('Malaysian Ringgit') }}</option>
											<option value="MXN">{{ _lang('Mexican Peso') }}</option>
											<option value="NOK">{{ _lang('Norwegian Krone') }}</option>
											<option value="NZD">{{ _lang('New Zealand Dollar') }}</option>
											<option value="PHP">{{ _lang('Philippine Peso') }}</option>
											<option value="PLN">{{ _lang('Polish Zloty') }}</option>
											<option value="GBP">{{ _lang('Pound Sterling') }}</option>
											<option value="SGD">{{ _lang('Singapore Dollar') }}</option>
											<option value="SEK">{{ _lang('Swedish Krona') }}</option>
											<option value="CHF">{{ _lang('Swiss Franc') }}</option>
											<option value="TWD">{{ _lang('Taiwan New Dollar') }}</option>
											<option value="THB">{{ _lang('Thai Baht') }}</option>
											<option value="TRY">{{ _lang('Turkish Lira') }}</option>
										</select>
									  </div>
									</div>
									
									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Secret Key') }}</label>						
										<input type="text" class="form-control" name="stripe_secret_key" value="{{ get_company_option('stripe_secret_key') }}">
									  </div>
									</div>
									
									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Publishable Key') }}</label>						
										<input type="text" class="form-control" name="stripe_publishable_key" value="{{ get_company_option('stripe_publishable_key') }}">
									  </div>
									</div>
								</div>
                            </div>

                            <br>
							<h5 class="header-title">{{ _lang('Razorpay Configuration') }}</h5>
							<div class="params-panel">								
								<div class="row">
									<div class="col-md-6">
									  	<div class="form-group">
											<label class="control-label">{{ _lang('Razorpay Active') }}</label>						
											<select class="form-control" name="razorpay_active" required>
											   <option value="no" {{ get_company_option('razorpay_active') == 'no' ? 'selected' : '' }}>{{ _lang('No') }}</option>
											   	<option value="yes" {{ get_company_option('razorpay_active') == 'yes' ? 'selected' : '' }}>{{ _lang('Yes') }}</option>
											</select>
									  	</div>
									</div>
									
									<div class="col-md-6">
									  	<div class="form-group">
											<label class="control-label">{{ _lang('Razorpay Currency') }}</label>			
											<select class="form-control select2 auto-select" data-selected="{{ get_company_option('razorpay_currency') }}" name="razorpay_currency" id="razorpay_currency" required>
												<option value="INR">{{ _lang('Indian Rupee') }}</option>
											</select>
									  	</div>
									</div>
									
									<div class="col-md-6">
									  	<div class="form-group">
											<label class="control-label">{{ _lang('Razorpay Key ID') }}</label>						
											<input type="text" class="form-control" name="razorpay_key_id" value="{{ get_company_option('razorpay_key_id') }}">
									  	</div>
									</div>
									
									<div class="col-md-6">
									  	<div class="form-group">
											<label class="control-label">{{ _lang('Razorpay Secret Key') }}</label>						
											<input type="text" class="form-control" name="razorpay_secret_key" value="{{ get_company_option('razorpay_secret_key') }}">
									  	</div>
									</div>
								</div>
                            </div>

                            <br>
							<h5 class="header-title">{{ _lang('Paystack Configuration') }}</h5>
							<div class="params-panel">								
								<div class="row">
									<div class="col-md-6">
									 	<div class="form-group">
											<label class="control-label">{{ _lang('Paystack Active') }}</label>						
											<select class="form-control" name="paystack_active" required>
											   <option value="no" {{ get_company_option('paystack_active') == 'no' ? 'selected' : '' }}>{{ _lang('No') }}</option>
											   <option value="yes" {{ get_company_option('paystack_active') == 'yes' ? 'selected' : '' }}>{{ _lang('Yes') }}</option>
											</select>
									  	</div>
									</div>

									<div class="col-md-6">
									 	 <div class="form-group">
											<label class="control-label">{{ _lang('Paystack Public Key') }}</label>	
											<input type="text" class="form-control" name="paystack_public_key" value="{{ get_company_option('paystack_public_key') }}">
									  	</div>
									</div>
									
									<div class="col-md-6">
									  	<div class="form-group">
											<label class="control-label">{{ _lang('Paystack Secret Key') }}</label>		
											<input type="text" class="form-control" name="paystack_secret_key" value="{{ get_company_option('paystack_secret_key') }}">
									  	</div>
									</div>


									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Paystack Currency') }}</label>
											<select class="form-control select2 auto-select" data-selected="{{ get_company_option('paystack_currency','GHS') }}" name="paystack_currency" id="paystack_currency" required>
												<option value="GHS">{{ _lang('Ghana') }}</option>
												<option value="NGN">{{ _lang('Nigeria') }}</option>
												<option value="ZAR">{{ _lang('South Africa') }}</option>
											</select>
										</div>
									</div>
								</div>
                            </div>
							
                            <div class="row">							
								<div class="col-md-12">
								  	<div class="form-group">
										<button type="submit" class="btn btn-primary">{{ _lang('Save Settings') }}</button>
								  	</div>
								</div>
							</div>
					   </form>	
				   </div>
				 </div>
			  </div>

			  
			  <div id="logo" class="tab-pane fade">
			     <div class="card">
				    <span class="d-none panel-title">{{ _lang('Logo Upload') }}</span>
				    <div class="card-body">
					   <form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('company/upload_logo') }}" enctype="multipart/form-data">				         
							
							{{ csrf_field() }}
							
							<div class="col-md-12">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Upload Logo') }}</label>						
								<input type="file" class="form-control dropify" name="logo" data-max-file-size="8M" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="{{ get_company_logo() }}" required>
							  </div>
							</div>
							
							<br>
							<div class="col-md-12">
							  <div class="form-group">
								<button type="submit" class="btn btn-primary btn-block">{{ _lang('Upload') }}</button>
							  </div>
							</div>	
							
					   </form>	
				   </div>
				 </div>
			  </div>
			  
		   </div>  
		</div>
	</div>
@endsection
