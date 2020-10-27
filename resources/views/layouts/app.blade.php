<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>{{ get_option('site_title', 'Elit Kit') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
		<!-- App favicon -->
        <link rel="shortcut icon" href="{{ get_favicon() }}">

		<!-- DataTables -->
        <link href="{{ asset('public/backend/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('public/backend/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
        
		<!-- Responsive datatable examples -->
        <link href="{{ asset('public/backend/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" /> 
		
		<link href="{{ asset('public/backend/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
		<link href="{{ asset('public/backend/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('public/backend/plugins/animate/animate.css') }}" rel="stylesheet" type="text/css">
		<link href="{{ asset('public/backend/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/backend/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" />
	    <link href="{{ asset('public/backend/plugins/jquery-toast-plugin/jquery.toast.min.css') }}" rel="stylesheet" />
        
		<!-- App Css -->
        <link rel="stylesheet" href="{{ asset('public/backend/assets/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('public/backend/assets/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('public/backend/assets/css/themify-icons.css') }}">
		<link rel="stylesheet" href="{{ asset('public/backend/assets/css/metisMenu.css') }}">
		<link rel="stylesheet" href="{{ asset('public/backend/assets/css/slicknav.min.css') }}">
		
		<!-- Others css -->
		<link rel="stylesheet" href="{{ asset('public/backend/assets/css/typography.css') }}">
		<link rel="stylesheet" href="{{ asset('public/backend/assets/css/default-css.css') }}">
		<link rel="stylesheet" href="{{ asset('public/backend/assets/css/styles.css?v=1.2') }}">
		<link rel="stylesheet" href="{{ asset('public/backend/assets/css/responsive.css?v=1.2') }}">
		
		<!-- Modernizr -->
		<script src="{{ asset('public/backend/assets/js/vendor/modernizr-2.8.3.min.js') }}"></script>
        
		<!--Chat Widget-->
		<link href="{{ asset('public/backend/assets/css/chat-widget.css?v=1.2') }}" rel="stylesheet" type="text/css" /> 

		@if(get_company_option('backend_direction',get_option('backend_direction')) == "rtl")
			<link rel="stylesheet" href="{{ asset('public/backend/assets/css/rtl/bootstrap.min.css') }}">
			<link rel="stylesheet" href="{{ asset('public/backend/assets/css/rtl/style.css') }}">
		@endif
		
		@include('layouts.others.languages')
		
    </head>

    <body>  
		<!-- Main Modal -->
		<div id="main_modal" class="modal" tabindex="-1" role="dialog">
		    <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
				    <div class="modal-header bg-primary">
						<h5 class="modal-title mt-0 text-white"></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
				    </div>
				  
				    <div class="alert alert-danger d-none m-3"></div>
				    <div class="alert alert-secondary d-none m-3"></div>			  
				    <div class="modal-body overflow-hidden"></div>
				  
				</div>
		    </div>
		</div>
		
		<!-- Secondary Modal -->
		<div id="secondary_modal" class="modal" tabindex="-1" role="dialog">
		    <div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				    <div class="modal-header bg-dark">
						<h5 class="modal-title mt-0 text-white"></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
				    </div>
				  
				    <div class="alert alert-danger d-none m-3"></div>
				    <div class="alert alert-secondary d-none m-3"></div>			  
				    <div class="modal-body overflow-hidden"></div>
				</div>
		    </div>
		</div>
	     
		<!-- Preloader area start -->
		<div id="preloader"></div>
		<!-- Preloader area end -->
		
		<div class="page-container">
		    <!-- sidebar menu area start -->
			<div class="sidebar-menu">
				<div class="sidebar-header">
					<div class="logo">
						<a href="{{ url('dashboard') }}"><img src="{{ Auth::user()->company_id != '' ? get_company_logo() : get_logo() }}" class="company-logo" alt="logo"></a>
					</div>
				</div>
				<div class="main-menu">
					<div class="menu-inner">
						<nav>
							<ul class="metismenu" id="menu">
							    <li><a href="{{ url('dashboard') }}"><i class="ti-dashboard"></i> <span>{{ _lang('Dashboard') }}</span></a></li>
								@include('layouts.menus.'.Auth::user()->user_type)
							</ul>
						</nav>
					</div>
				</div>
			</div>
			<!-- sidebar menu area end -->
		
        
			<!-- main content area start -->
			<div class="main-content">

				<!-- header area start -->
				<div class="header-area">
					<div class="row align-items-center">
						<!-- nav and search button -->
						<div class="col-md-6 col-sm-8 clearfix">
							<div class="nav-btn float-left">
								<span></span>
								<span></span>
								<span></span>
							</div>
						</div>

						<!-- profile info & task notification -->
						<div class="col-md-6 col-sm-4 clearfix">

							<ul class="notification-area float-right">
	                            @if(Auth::user()->user_type != 'admin')
									<li class="dropdown">
										<i class="ti-bell dropdown-toggle" data-toggle="dropdown">
											<span>{{ Auth::user()->unreadNotifications->count() }}</span>
										</i>
										<div class="dropdown-menu bell-notify-box notify-box">
											<span class="notify-title">{{ _lang('You have').' '.Auth::user()->unreadNotifications->count().' '._lang('new notifications') }}</span>
											<div class="nofity-list">
												@foreach (Auth::user()->notifications->take(15) as $notification)
													<a href="{{ url('notification/'.$notification->id) }}" class="notify-item {{ $notification->read_at == null ? 'unread-notification' : '' }}">
														<div class="notify-thumb">
															<img src="{{ asset('public/uploads/profile/'.$notification->user->profile_picture) }}">
														</div>
														<div class="notify-text">
															<p><b>{{ $notification->user->name }}</b> {{ $notification->data['title'] }}</p>
															<span>{{ $notification->data['content'] }}</span><br>
															<span>{{ $notification->created_at->diffForHumans() }}</span>
														</div>
													</a>
												@endforeach
											</div>
										</div>
									</li>
								@endif

	                            <li>
									<div class="user-profile">
										<h4 class="user-name dropdown-toggle" data-toggle="dropdown">
											<img class="avatar user-thumb" id="my-profile-img" src="{{ Auth::user()->profile_picture != '' ? asset('public/uploads/profile/'.Auth::user()->profile_picture) :  asset('public/images/avatar.png') }}" alt="avatar"> {{ Auth::user()->name }} <i class="fa fa-angle-down"></i>
										</h4>
										<div class="dropdown-menu">
											@if(Auth::user()->user_type == 'user' && get_option('membership_system') == 'enabled')
												<a class="dropdown-item" href="{{ url('membership/my_subscription') }}"><i class="ti-package text-muted mr-2"></i> {{ _lang('My Subscription') }}</a>

												<a class="dropdown-item" href="{{ url('membership/extend') }}"><i class="ti-wallet text-muted mr-2"></i> {{ _lang('Upgrade Subscription') }}</a>
											@endif
											<a class="dropdown-item" href="{{ url('profile/edit') }}"><i class="ti-settings text-muted mr-2"></i> {{ _lang('Profile Settings') }}</a>
											<a class="dropdown-item" href="{{ url('profile/change_password') }}"><i class="ti-pencil text-muted mr-2"></i> {{ _lang('Change Password') }}</a>
											<div class="dropdown-divider mb-0"></div>
											<a class="dropdown-item" href="{{ url('logout') }}"><i class="ti-power-off text-muted mr-2"></i> {{ _lang('Logout') }}</a>
										</div>
									</div>
	                            </li>
	                            
	                        </ul>

						</div>
					</div>
				</div><!-- header area end -->
				
				<!-- page title area start -->
				<div class="page-title-area mb-3">
					<div class="row align-items-center py-3">
						<div class="col-sm-12">
							<div class="breadcrumbs-area clearfix">
								<h4 class="page-title float-left">{{ _lang('Dashboard') }}</h4>
								<ul class="breadcrumbs float-left">
									@php $segments = ''; @endphp
									@foreach(Request::segments() as $segment)
										
										@if ($segment == "dashboard")
											@php continue; @endphp
										@endif
										
										@php $segments .= '/'.$segment; @endphp
										
										@if(is_numeric($segment) || strlen($segment) > 30)
										   @php $segment = 'View'; @endphp
										@endif
										
										@if(! checkRoute($segments))
											@php continue; @endphp
										@endif
										
										@if(! $loop->last)
											<li>
												<a href="{{ url($segments) }}">{{ ucwords(str_replace("_"," ",$segment)) }}</a>
											</li>
										@else
											<li>
												<span>{{ ucwords(str_replace("_"," ",$segment)) }}</span>
											</li>
										@endif
									@endforeach
								</ul>
							</div>
						</div>
					</div>
				</div><!-- page title area end -->
				
				<div class="main-content-inner">
					<!-- Trial and Membership Alert -->
					@php $user = Auth::user(); @endphp

					@if(has_membership_system() == 'enabled' && $user->user_type == 'user')
						
					    @if( membership_validity() < date('Y-m-d'))
							<div class="alert alert-danger">
							   <b class="float-left pt-2">{{ _lang('Please make your membership payment for further process !') }}</b>
							   <a href="{{ url('membership/extend') }}" class="btn btn-primary btn-xs float-right"><b>{{ _lang('Pay Now') }}</b></a>
							   <div class="clearfix"></div>
							</div>
						@endif
						
						@if( $user->company->membership_type == 'trial' && membership_validity() > date('Y-m-d'))
							<div class="alert alert-warning">
							   <b>{{ _lang('You Are Currenly Using Trial Account !') }}&emsp;<a href="{{ url('membership/extend') }}" class="btn btn-danger btn-xs">{{ _lang('Upgrade Now') }}</a></b>
							</div>
						@endif
						
					@endif
					<!-- End Trial and Membership Alert -->
					
					<div class="alert alert-success alert-dismissible mt-5" id="main_alert" role="alert">
						<button type="button" id="close_alert" class="close">
							<span aria-hidden="true"><i class="far fa-times-circle"></i></span>
						</button>
						<span class="msg"></span>
					</div>
					
					@yield('content')

					
					@if(get_option('live_chat') == 'enabled' && has_feature('live_chat'))
						@if(! Request::is('live_chat') && Auth::user()->user_type != 'admin')
							@include('backend.live_chat.chat-widget')
						@endif
					@endif
					
					<audio id="chatSound">
					  <source src="{{ asset('public/sounds/messenger.mp3') }}" type="audio/mpeg" muted>
					</audio>
				</div><!--End main content Inner-->
				
			</div><!--End main content-->
			
			<!-- footer area start-->
			<footer>
				<div class="footer-area">
					<p>&copy; {{ date('Y').' '.get_option('company_name') }}</p>
				</div>
			</footer>
			<!-- footer area end-->
		</div><!--End Page Container-->

        <!-- jQuery  -->
		<script src="{{ asset('public/backend/assets/js/vendor/jquery-2.2.4.min.js') }}"></script>
		<script src="{{ asset('public/backend/assets/js/popper.min.js') }}"></script>
		<script src="{{ asset('public/backend/assets/js/bootstrap.min.js') }}"></script>
		<script src="{{ asset('public/backend/assets/js/metisMenu.min.js') }}"></script>
		<script src="{{ asset('public/backend/assets/js/jquery.slimscroll.min.js') }}"></script>
		<script src="{{ asset('public/backend/assets/js/jquery.slicknav.min.js') }}"></script>
        
		<script src="{{ asset('public/backend/assets/js/print.js') }}"></script>
		<script src="{{ asset('public/backend/assets/js/pace.min.js') }}"></script>
		<script src="{{ asset('public/backend/assets/js/clipboard.min.js') }}"></script>
        <script src="{{ asset('public/backend/plugins/moment/moment.js') }}"></script>
        
		<!-- Dashboard Scripts -->
		@if(Request::is('dashboard'))
			<script src="{{ asset('public/backend/plugins/echart/echarts.min.js') }}"></script>
		@endif
		
		<!-- Required datatable js -->
        <script src="{{ asset('public/backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('public/backend/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <!-- datatable Buttons -->
        <script src="{{ asset('public/backend/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('public/backend/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('public/backend/plugins/datatables/jszip.min.js') }}"></script>
        <script src="{{ asset('public/backend/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('public/backend/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('public/backend/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('public/backend/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('public/backend/plugins/datatables/buttons.colVis.min.js') }}"></script>
        <!-- Responsive datatable -->
        <script src="{{ asset('public/backend/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('public/backend/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

		<script src="{{ asset('public/backend/plugins/dropify/js/dropify.min.js') }}"></script>
		<script src="{{ asset('public/backend/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
		<script src="{{ asset('public/backend/plugins/select2/select2.min.js') }}"></script>
		<script src="{{ asset('public/backend/plugins/daterangepicker/daterangepicker.js') }}"></script>
		<script src="{{ asset('public/backend/plugins/tinymce/tinymce.min.js') }}"></script>
		<script src="{{ asset('public/backend/plugins/parsleyjs/parsley.min.js') }}"></script>
		<script src="{{ asset('public/backend/plugins/jquery-toast-plugin/jquery.toast.min.js') }}"></script>

        <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
		
        <!-- App js -->
        <script src="{{ asset('public/backend/assets/js/scripts.js?v=1.7') }}"></script>

		<script type="text/javascript">		
		(function($){

			"use strict";	
			
			@if(Request::is('dashboard'))
				$(".page-title").html("{{ _lang('Dashboard') }}"); 
			@else
                $(".page-title").html($(".title").html()); 
				$(".page-title").html($(".panel-title").html());
			@endif			

			
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
				
					$("input[name='" + name + "']").addClass('error is-invalid');
					$("select[name='" + name + "'] + span").addClass('error is-invalid');
				
					$("input[name='"+name+"'], select[name='"+name+"']").parent().append("<span class='v-error'>{{$error}}</span>");
				@endif
				@php $i++; @endphp
			
			@endforeach
			
        })(jQuery); <!-- End jQuery -->

	 </script>
	 
	 @if( get_option('live_chat') == 'enabled' && has_feature('live_chat'))
		<script src="{{ asset('public/backend/assets/js/socket.js?v=1.2') }}" defer></script>
		
		@if(! Request::is('live_chat'))
		<script src="{{ asset('public/backend/assets/js/chat-widget.js?v=1.2') }}" defer></script>
		@endif
		
	 @endif
	 
	 <!-- Custom JS -->
	 @yield('js-script')
		
    </body>
</html>