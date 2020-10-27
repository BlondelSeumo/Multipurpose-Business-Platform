@extends('theme.default.layouts.website')

@section('header')
<div class="page-hero bg_cover" style="background-image: url({{ get_option('sub_banner_image') != '' ? asset('public/uploads/media/'.get_option('sub_banner_image')) : theme_asset('assets/images/header-bg.jpg') }})">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-8 col-lg-10">
				<div class="header-content text-center">
					<h3 class="header-title">{{ _lang('Sign Up') }}</h3>
				</div> <!-- header content -->
			</div>
		</div> <!-- row -->
	</div> <!-- container -->
</div> <!-- header content -->
@endsection

@section('content')

<!--====== Sign Up PART START ======-->
<section id="sign_up" class="general-area">
	<div class="container">
		<div class="row justify-content-left">
			<div class="col-lg-6">
				<div class="section-title text-left pb-10">
					<h4 class="title">{{ _lang('Create Your Account') }}</h4>
					<p class="text">{{ _lang('Stop wasting time and money designing and managing a website that does not get results. Happiness guaranteed!') }}</p>
				</div> <!-- section title -->
			</div>
		</div> <!-- row -->
		<div class="row">
			<div class="col-lg-7 mt-40">
				<div class="auth-form">
					<form action="{{ route('register') }}" method="post" autocomplete="off">
						@csrf
						<div class="row">
							<div class="col-md-12">
								<div class="single-form form-group">
									<input type="text" name="business_name" value="{{ old('business_name') }}" placeholder="{{ _lang('Your Business Name') }}" required="required">
									@if ($errors->has('business_name'))
	                                    <div class="help-block with-errors">{{ $errors->first('business_name') }}</div>
	                                @endif
								</div> <!-- single form -->
							</div>
							
							<div class="col-md-6">
								<div class="single-form form-group">
									<input type="text" name="name" value="{{ old('name') }}" placeholder="{{ _lang('Your Name') }}" required="required">
									@if ($errors->has('name'))
	                                    <div class="help-block with-errors">{{ $errors->first('name') }}</div>
	                                @endif
								</div> <!-- single form -->
							</div>
							
							<div class="col-md-6">
								<div class="single-form form-group">
									<input type="email" name="email" value="{{ old('email') }}" placeholder="{{ _lang('Your Email') }}" required="required">
									@if ($errors->has('email'))
	                                    <div class="help-block with-errors">{{ $errors->first('email') }}</div>
	                                @endif
								</div> <!-- single form -->
							</div>
							<div class="col-md-6">
								<div class="single-form form-group">
									<input type="password" name="password" placeholder="{{ _lang('Password') }}" required="required">
									@if ($errors->has('password'))
	                                    <div class="help-block with-errors">{{ $errors->first('password') }}</div>
	                                @endif
								</div> <!-- single form -->
							</div>
							<div class="col-md-6">
								<div class="single-form form-group">
									<input type="password" name="password_confirmation" placeholder="{{ _lang('Confirm Password') }}" required="required">
									<div class="help-block with-errors"></div>
								</div> <!-- single form -->
							</div>

							<div class="col-md-6">
								<div class="single-form form-group">
									<select id="package" name="package" required>
										<option value="">{{ _lang('Select Package') }}</option>
										{{ create_option('packages', 'id', 'package_name', isset($_GET['package']) ? $_GET['package'] : old('package')) }}
									</select>
									@if ($errors->has('package'))
	                                    <div class="help-block with-errors">{{ $errors->first('package') }}</div>
	                                @endif  
								</div>									
							</div>
							
							<div class="col-md-6">
								<div class="single-form form-group">
									<select name="package_type" id="package_type" required>
										<option value="">{{ _lang('Package Type') }}</option>
										<option value="monthly">{{ _lang('Monthly Pack') }}</option>
										<option value="yearly">{{ _lang('Yearly Pack') }}</option> 
									</select> 
									@if ($errors->has('package_type'))
	                                    <div class="help-block with-errors">{{ $errors->first('package_type') }}</div>
	                                @endif   
								</div>									
							</div>								

							<div class="col-md-12">
								<div class="single-form form-group text-center">
									<button type="submit" id="create_account" class="main-btn">{{ _lang('Create Account') }}</button>
									<br><a href="{{ url('register/client_signup') }}" class="btn-link mt-2">{{ _lang('Client Sign Up') }}</a>
								</div> <!-- single form -->
							</div>
						</div>
					</form>
				</div>					
			</div> <!-- row -->
			<div class="col-lg-5 mt-40">
				<div class="image">
					<img src="{{ asset('public/theme/default/assets/images/auth.png') }}" alt="features">
				</div>
			</div>
			
		</div> <!-- row -->
	</div> <!-- conteiner -->

</section>

<!--====== Sign Up PART ENDS ======-->
@endsection

@section('js-script')
<script>

var package_type = "{{ isset($_GET['package_type']) ? $_GET['package_type'] : '' }}";
$("#package_type").val(package_type);

</script>
@endsection