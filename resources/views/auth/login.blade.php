@extends('layouts.login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card card-signin my-5">
                <div class="login-form-head">
                    <h4>{{ _lang('Sign In') }}</h4>     
                </div>
                <div class="card-body">

                    @if(Session::has('error'))
                        <div class="alert alert-danger text-center">
                            <strong>{{ session('error') }}</strong>
                        </div>
                    @endif
					
					@if(Session::has('registration_success'))
                        <div class="alert alert-success text-center">
                            <strong>{{ session('registration_success') }}</strong>
                        </div>
                    @endif

                    <img class="logo" src="{{ get_logo() }}">
					<form method="POST" class="form-signin" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="{{ _lang('Email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
						    <div class="col-md-12">	

								<input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ _lang('Password') }}" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="text-center">
							<div class="custom-control custom-checkbox mb-3">
								<input type="checkbox" name="remember" class="custom-control-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
								<label class="custom-control-label" for="remember">{{ _lang('Remember Me') }}</label>
							</div>
						</div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-block">
                                    {{ _lang('Login') }}
                                </button>
								
								@if(get_option('google_login') == 'enabled')
									<a href="{{ url('google/redirect') }}" class="btn btn-danger btn-block">{{ _lang('Continue with Google') }}</a>
								@endif
								
								@if(get_option('allow_singup','yes') == 'yes')
									<a class="btn btn-link btn-register" href="{{ get_option('website_enable') == 'yes' ? url('/sign_up') : url('register') }}">
										{{ _lang('Create Account') }}
									</a>
								@endif
                            </div>
                        </div>
						
						
						<div class="form-group row">
                            <div class="col-md-12">
							    <a class="btn-link" href="{{ url('register/client_signup') }}">
									{{ _lang('Client Sign Up') }}
								</a>
								&nbsp | &nbsp;
								<a class="btn-link" href="{{ route('password.request') }}">
									{{ _lang('Forgot Password?') }}
								</a>
							</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
