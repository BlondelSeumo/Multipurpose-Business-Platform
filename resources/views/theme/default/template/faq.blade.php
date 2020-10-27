@extends('theme.default.layouts.website')

@section('header')
<div class="page-hero bg_cover" style="background-image: url({{ get_option('sub_banner_image') != '' ? asset('public/uploads/media/'.get_option('sub_banner_image')) : theme_asset('assets/images/header-bg.jpg') }})">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-8 col-lg-10">
				<div class="header-content text-center">
					<h3 class="header-title">{{ _lang('FAQ') }}</h3>
				</div> <!-- header content -->
			</div>
		</div> <!-- row -->
	</div> <!-- container -->
</div> <!-- header content -->
@endsection

@section('content')

<!--====== FAQ PART START ======-->
<section id="faq" class="general-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-title text-center pb-10">
                    <h4 class="title">{{ _lang('FAQ') }}</h4>
                    <p class="text">{{ _lang('Stop wasting time and money designing and managing a website that does not get results. Happiness guaranteed!') }}</p>
                </div> <!-- section title -->
            </div>
        </div> <!-- row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="row mt-5" id="accordion">
					
					@foreach(\App\Faq::all() as $faq)
					<div class="col-md-12">
						  <div class="card faq-box">
							<div class="card-header">
							  <a class="card-link" data-toggle="collapse" href="#collapse-{{ $faq->id }}">
							  {{ get_array_data($faq->question) }}
							  </a>
							</div>
							<div id="collapse-{{ $faq->id }}" class="collapse {{ $loop->first ? 'show' : '' }}" data-parent="#accordion">
							  <div class="card-body">
								{!! clean(get_array_data($faq->answer)) !!}
							  </div>
							</div>
						  </div>
					</div>
					@endforeach

                </div> <!-- row -->
            </div> <!-- row -->
        </div> <!-- row -->
    </div> <!-- conteiner -->
</section>

<!--====== FAQ PART ENDS ======-->

@endsection