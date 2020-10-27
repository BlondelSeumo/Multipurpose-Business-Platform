<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{{ get_option('site_title', 'ElitKit') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

		<!-- App favicon -->
        <link rel="shortcut icon" href="{{ get_favicon() }}">
	   
        <!-- App css -->
        <link href="{{ asset('public/backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" media="all"  type="text/css" />
		<link href="{{ asset('public/backend/assets/css/fontawesome.min.css') }}" rel="stylesheet">
		<link href="{{ asset('public/backend/assets/css/themify-icons.css') }}" rel="stylesheet">
		<link href="{{ asset('public/backend/assets/css/styles.css') }}" rel="stylesheet" media="all" type="text/css" />
    </head>

    <body>  
        <div class="page-wrapper">
            <!-- Page Content-->
            <div class="page-content pt-5">
                <div class="container">
                    <!-- Page-Title -->
					<div class="alert alert-success alert-dismissible" id="main_alert" role="alert">
						<button type="button" id="close_alert" class="close">
							<span aria-hidden="true"><i class="mdi mdi-close"></i></span>
						</button>
						<span class="msg"></span>
					</div>
                    
					@yield('content')
					
                </div><!-- container -->

                <footer class="footer text-center">
                    <span>&copy; {{ date('Y').' '.get_option('company_name') }}</span>
                </footer><!--end footer-->
            </div>
            <!-- end page content -->
        </div>
        <!-- end page-wrapper -->


        <!-- jQuery  -->
        <script src="{{ asset('public/backend/assets/js/vendor/jquery-2.2.4.min.js') }}"></script>
        <script src="{{ asset('public/backend/assets/js/popper.min.js') }}"></script>
		<script src="{{ asset('public/backend/assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('public/backend/assets/js/print.js') }}"></script>
		<script src="{{ asset('public/backend/plugins/toastr/toastr.js') }}"></script>
		<script src="{{ asset('public/backend/assets/js/public.js') }}"></script>
		
		<script type="text/javascript">		
		(function($){
			"use strict";	

			//Show Success Message
			@if(Session::has('success'))
		       $("#main_alert > span.msg").html(" {{ session('success') }} ");
			   $("#main_alert").addClass("alert-success").removeClass("alert-danger");
			   $("#main_alert").css('display','block');
			@endif
			
			//Show Single Error Message
			@if(Session::has('error'))
			   $("#main_alert > span.msg").html(" {{ session('error') }} ");
			   $("#main_alert").addClass("alert-danger").removeClass("alert-success");
			   $("#main_alert").css('display','block');
			@endif
			
			
			@php $i =0; @endphp

			@foreach ($errors->all() as $error)
			    @if ($loop->first)
					$("#main_alert > span.msg").html("<i class='typcn typcn-delete'></i> {{ $error }} ");
					$("#main_alert").addClass("alert-danger").removeClass("alert-success");
				@else
                    $("#main_alert > span.msg").append("<br><i class='typcn typcn-delete'></i> {{ $error }} ");					
				@endif
				
				@if ($loop->last)
					$("#main_alert").css('display','block');
				@endif
				
				@if(isset($errors->keys()[$i]))
					var name = "{{ $errors->keys()[$i] }}";
				
					$("input[name='" + name + "']").addClass('error');
					$("select[name='" + name + "'] + span").addClass('error');
				
					$("input[name='"+name+"'], select[name='"+name+"']").parent().append("<span class='v-error'>{{$error}}</span>");
				@endif
				@php $i++; @endphp
			
			@endforeach
			
        })(jQuery);
		
	 </script>

	 <!-- Custom JS -->
	 @yield('js-script')
	 
    </body>
</html>