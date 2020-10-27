<!doctype html>
<html lang="en">

<head>
    <!--====== Required meta tags ======-->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
	<!--====== Title ======-->
    <title>{{ get_option('website_title','Elit Kit') }}</title>
	
    <meta name="keywords" content="{{ get_option('meta_keywords') }}"/>
    <meta name="description" content="{{ get_option('meta_description') }}"/>

    <meta name="og:title" content="{{ get_array_option('hero_title') }}"/>
    <meta name="og:type" content="website"/>
    <meta name="og:url" content="{{ url('') }}"/>
    <meta name="og:image" content="{{ asset('public/images/meta-image.png') }}"/>
    <meta name="og:site_name" content="{{ get_option('website_title','ElitKit') }}"/>
    <meta name="og:description" content="{{ get_option('meta_description') }}"/>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ get_favicon() }}">

    <!--====== Bootstrap css ======-->
    <link rel="stylesheet" href="{{ asset('public/theme/default/assets/css/bootstrap.min.css') }}">

    <!--====== Line Icons css ======-->
    <link rel="stylesheet" href="{{ asset('public/theme/default/assets/css/LineIcons.css') }}">

    <!--====== Magnific Popup css ======-->
    <link rel="stylesheet" href="{{ asset('public/theme/default/assets/css/magnific-popup.css') }}">

    <!--====== Toastr css ======-->
    <link rel="stylesheet" href="{{ asset('public/theme/default/assets/css/toastr.css') }}">

    <!--====== Default css ======-->
    <link rel="stylesheet" href="{{ asset('public/theme/default/assets/css/default.css') }}">

    <!--====== Style css ======-->
    <link rel="stylesheet" href="{{ asset('public/theme/default/assets/css/style.css?v=1.4') }}">

    <!--- Custom CSS Code --->
    <style type="text/css">
        {!! xss_clean(get_option('custom_css_code')) !!}
    </style>
    
</head>

<body>

    <!--====== HEADER PART START ======-->

    <header class="header-area">
        <div class="navgition navgition-transparent">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg">
                            <a class="navbar-brand" href="#">
                                <img src="{{ get_logo() }}" class="logo" alt="Logo">
                            </a>

                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarOne" aria-controls="navbarOne" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                            </button>

                            <div class="collapse navbar-collapse sub-menu-bar" id="navbarOne">
                                
                                @if(Request::is('/'))
                                    <ul class="navbar-nav m-auto">
                                        <li class="nav-item active">
                                            <a class="page-scroll" href="#home">{{ _lang('HOME') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="page-scroll" href="{{ url('site/features') }}">{{ _lang('FEATURES') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="page-scroll" href="{{ url('site/pricing') }}">{{ _lang('PRICING') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="page-scroll" href="#contact">{{ _lang('CONTACT') }}</a>
                                        </li>
                                    </ul>
                                @else 
                                    <ul class="navbar-nav m-auto">
                                        <li class="nav-item">
                                            <a class="page-scroll" href="{{ url('/') }}">{{ _lang('HOME') }}</a>
                                        </li>
                                        <li class="nav-item {{ Request::is('site/features') ? 'active' : '' }}">
                                            <a class="page-scroll" href="{{ url('site/features') }}">{{ _lang('FEATURES') }}</a>
                                        </li>
                                        <li class="nav-item {{ Request::is('site/pricing') ? 'active' : '' }}">
                                            <a class="page-scroll" href="{{ url('site/pricing') }}">{{ _lang('PRICING') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="page-scroll" href="{{ url('/') }}#contact">{{ _lang('CONTACT') }}</a>
                                        </li>
                                    </ul>
                                @endif
								
                                
								<ul class="navbar-nav m-auto auth-nav">
                                    @if(! Auth::check())
                                        <li class="nav-item">
                                            <a class="sing-in-btn" href="{{ url('login') }}">{{ _lang('SIGN IN') }}</a>
                                        </li>
										@if(get_option('allow_singup','yes') == 'yes')
											<li class="nav-item">
												<a class="sing-up-btn" href="{{ url('sign_up') }}">{{ _lang('SIGN UP') }}</a>
											</li>
										@endif
                                    @else
                                        <li class="nav-item">
                                            <a class="sing-up-btn" href="{{ url('dashboard') }}"><i class='lni lni-dashboard'></i>&nbsp;{{ _lang('Dashboard') }}</a>
                                            <a class="sing-in-btn" href="{{ url('logout') }}"><i class='lni lni-exit'></i>&nbsp;{{ _lang('SIGN OUT') }}</a>
                                        </li>
                                    @endif
									
									@if(get_option('website_language_dropdown','yes') == 'yes')
										<div class="language-picker">
											<div class="dropdown">
											  <button class="btn btn-outline-primary dropdown-toggle" type="button" id="select_language" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											  {{ session('language') =='' ? get_option('language') : session('language') }}
											  </button>
											  <div class="dropdown-menu" aria-labelledby="select_language">
												@foreach(get_language_list() as $language)
													<a class="dropdown-item" href="?language={{ $language }}">{{ $language }}</a>
												@endforeach
											  </div>
											</div>
										</div>
									@endif
                                </ul>
                               
                            </div>

                        </nav> <!-- navbar -->
                    </div>
                </div> <!-- row -->
            </div> <!-- container -->
        </div> <!-- navgition -->

        @if(Request::is('/'))
            <div id="home" class="header-hero bg_cover" style="background-image: url({{ get_array_option('home_banner_image') != '' ? asset('public/uploads/media/'.get_array_option('home_banner_image')) : theme_asset('assets/images/header-bg.jpg') }})">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-10">
                            <div class="header-content text-center">
                                <h3 class="header-title">{{ get_array_option('hero_title') }}</h3>
                                <p class="text">{{ get_array_option('hero_sub_title') }}</p>
                                <ul class="header-btn">
                                    @if(get_option('trial_period') != 0)
										<li><a class="main-btn btn-one" href="{{ Auth::check() ? url('dashboard') : url('sign_up') }}">{{ _lang('Start') . ' ' . get_option('trial_period') . ' ' . _lang('Days Trial') }}</a></li>
									@else
										<li><a class="main-btn btn-one" href="{{ Auth::check() ? url('dashboard') : url('sign_up') }}">{{ _lang('Get Started') }}</a></li>
									@endif
 
                                    @if(get_option('promo_video_url') != '')
                                        <li><a class="main-btn btn-two video-popup" href="{{ get_option('promo_video_url') }}">{{ _lang('WATCH THE VIDEO') }} <i class="lni lni-play"></i></a></li>
                                    @endif
                                </ul>
                            </div> <!-- header content -->
                        </div>
                    </div> <!-- row -->
                </div> <!-- container -->
                <div class="header-shape">
                    <img src="{{ asset('public/theme/default/assets/images/header-shape.svg') }}" alt="shape">
                </div>
            </div> <!-- header content -->
        @else
            @yield('header')
        @endif
    </header>

    <!--====== HEADER PART ENDS ======-->

    @yield('content')

    <!--====== FOOTER PART START ======-->

    <footer id="footer" class="footer-area">
        <div class="footer-widget">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer-logo-support d-md-flex align-items-end justify-content-between">
                            <div class="footer-logo d-flex align-items-end">
                                <a class="mt-30" href="{{ url('') }}"><img src="{{ get_logo() }}" alt="Logo"></a>
                                
								@php
									$facebook_link = get_option('facebook_link');
									$twitter_link = get_option('twitter_link');
									$instagram_link = get_option('instagram_link');
									$linkedin_link = get_option('linkedin_link');
								@endphp
								
                                <ul class="social mt-30">
                                    @if($facebook_link)
										<li><a href="{{ $facebook_link }}"><i class="lni lni-facebook-filled"></i></a></li>
                                    @endif
									
									@if($twitter_link)
										<li><a href="{{ $twitter_link }}"><i class="lni lni-twitter-original"></i></a></li>
                                    @endif
									
									@if($instagram_link)
										<li><a href="{{ $instagram_link }}"><i class="lni lni-instagram-original"></i></a></li>
                                    @endif
									
									@if($linkedin_link)
										<li><a href="{{ $linkedin_link }}"><i class="lni lni-linkedin-original"></i></a></li>
                                    @endif
								</ul>
                            </div> <!-- footer logo -->
                            
                        </div> <!-- footer logo support -->
                    </div>
                </div> <!-- row -->
                <div class="row">
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="footer-link">
                            <h6 class="footer-title">{{ _lang('Company') }}</h6>
                            <ul>
                                <li><a href="{{ url('site/about') }}">{{ _lang('About') }}</a></li>
                                <li><a href="{{ url('/') }}#contact">{{ _lang('Contact') }}</a></li>
                            </ul>
                        </div> <!-- footer link -->
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="footer-link">
                            <h6 class="footer-title">{{ _lang('Pricing & features') }}</h6>
                            <ul>
                                <li><a href="{{ url('site/pricing') }}">{{ _lang('Pricing') }}</a></li>
                                <li><a href="{{ url('site/features') }}">{{ _lang('Features') }}</a></li>
                            </ul>
                        </div> <!-- footer link -->
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-5">
                        <div class="footer-link">
                            <h6 class="footer-title">{{ _lang('Help & Suuport') }}</h6>
                            <ul>
                                <li><a href="{{ url('site/faq') }}">{{ _lang('FAQ') }}</a></li>
                                <li><a href="{{ url('site/terms_condition') }}">{{ _lang('Terms & Conditions') }}</a></li>
                            </ul>
                        </div> <!-- footer link -->
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-7">
                        <div class="footer-newsletter">
                            <h6 class="footer-title">{{ _lang('Subscribe Newsletter') }}</h6>
                            <div class="newsletter">
                                <form action="{{ url('emaiL_subscribed') }}" method="post">
                                    {{ csrf_field() }}
                                    <input type="email" name="email" placeholder="{{ _lang('Your Email') }}" required>
                                    <button type="submit"><i class="lni lni-angle-double-right"></i></button>
                                </form>
                            </div>
                            <p class="text">{{ _lang('Subscribe weekly newsletter to stay upto date. We do not send spam') }}.</p>
                        </div> <!-- footer newsletter -->
                    </div>
                </div> <!-- row -->
            </div> <!-- container -->
        </div> <!-- footer widget -->
        
        <div class="footer-copyright">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="copyright text-center">
                            <p class="text">{!! clean(get_array_option('website_copyright','','Design & Developed by TrickyCode')) !!}</p>
                        </div>
                    </div>
                </div> <!-- row -->
            </div> <!-- container -->
        </div> <!-- footer copyright -->
    </footer>

    <!--====== FOOTER PART ENDS ======-->

    <!--====== BACK TO TOP PART START ======-->

    <a class="back-to-top" href="#"><i class="lni lni-chevron-up"></i></a>

    <!--====== BACK TO TOP PART ENDS ======-->



    <!--====== jquery js ======-->
    <script src="{{ asset('public/theme/default/assets/js/vendor/modernizr-3.6.0.min.js') }}"></script>
    <script src="{{ asset('public/theme/default/assets/js/vendor/jquery-1.12.4.min.js') }}"></script>

    <!--====== Bootstrap js ======-->
    <script src="{{ asset('public/theme/default/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/theme/default/assets/js/popper.min.js') }}"></script>

    <!--====== Scrolling Nav js ======-->
    <script src="{{ asset('public/theme/default/assets/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('public/theme/default/assets/js/scrolling-nav.js') }}"></script>

    <!--====== Magnific Popup js ======-->
    <script src="{{ asset('public/theme/default/assets/js/jquery.magnific-popup.min.js') }}"></script>

    <!--====== Tostr js ======-->
    <script src="{{ asset('public/theme/default/assets/js/toastr.js') }}"></script>

    <!--====== Main js ======-->
    <script src="{{ asset('public/theme/default/assets/js/main.js') }}"></script>

    <script type="text/javascript">     
        (function($){		
			"use strict";
			
            // Show Success Message
            @if(Session::has('success'))
               Command: toastr["success"](" {{ session('success') }} ")
            @endif
            
            // Show Single Error Message
            @if(Session::has('error'))
               Command: toastr["error"]("{{ session('error') }}")
            @endif

            @foreach ($errors->all() as $error)
                Command: toastr["error"]("{{ $error }}");          
            @endforeach
			
        })(jQuery); <!-- End jQuery -->

        @if(! Request::is('/'))
		  document.title = $(".header-title").html() + ' | ' + document.title;
        @endif
    </script>

     <!-- Custom JS -->
     @yield('js-script')

</body>

</html>
