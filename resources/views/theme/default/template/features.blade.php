@extends('theme.default.layouts.website')

@section('header')
<div class="page-hero bg_cover" style="background-image: url({{ get_option('sub_banner_image') != '' ? asset('public/uploads/media/'.get_option('sub_banner_image')) : theme_asset('assets/images/header-bg.jpg') }})">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-8 col-lg-10">
				<div class="header-content text-center">
					<h3 class="header-title">{{ _lang('Features') }}</h3>
				</div> <!-- header content -->
			</div>
		</div> <!-- row -->
	</div> <!-- container -->
</div> <!-- header content -->
@endsection

@section('content')

<!--====== Features PART START ======-->

<section id="feature" class="features-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-title text-center pb-10">
                    <h4 class="title">{{ _lang('Core Features') }}</h4>
                    <p class="text">{{ _lang('Stop wasting time and money designing and managing a website that does not get results. Happiness guaranteed!') }}</p>
                </div> <!-- section title -->
            </div>
        </div> <!-- row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="row">

                    @foreach(\App\Feature::all() as $feature)
                    <div class="col-md-4">
                        <div class="features-content feature-box mt-40 d-sm-flex">
                            <div class="features-icon">
                                {!! strip_tags(get_array_data($feature->icon),'<i>') !!}
                            </div>
                            <div class="features-content media-body">
                                <h4 class="features-title">{{ get_array_data($feature->title) }}</h4>
                                <p class="text">{{ get_array_data($feature->content) }}</p>
                            </div>
                        </div> <!-- features content -->
                    </div>
                    @endforeach
        
                </div> <!-- row -->
            </div> <!-- row -->
        </div> <!-- row -->
    </div> <!-- conteiner -->
</section>

<!--====== featureS PART ENDS ======-->

@endsection
