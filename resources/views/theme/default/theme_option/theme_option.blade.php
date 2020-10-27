@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-3">
	    <ul class="nav flex-column nav-tabs settings-tab" role="tablist">
	      <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#global_settings">{{ _lang('Global Settings') }}</a></li>
		  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#home_page">{{ _lang('Home Page') }}</a></li>
		  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#about_page">{{ _lang('About Page') }}</a></li>
		  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#terms_page">{{ _lang('Terms & Condition') }}</a></li>
		  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#seo">{{ _lang('SEO Settings') }}</a></li>
		  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#social_links">{{ _lang('Social Links') }}</a></li>
		  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#custom_css">{{ _lang('Custom CSS') }}</a></li>
	    </ul>
	</div>

	@php $language_list = get_language_list(); @endphp
	  
	<div class="col-sm-9">
		<div class="tab-content">

			<div id="global_settings" class="tab-pane active">
				<div class="card">
				    <div class="card-body">
					<h4 class="mb-4 header-title panel-title">{{ _lang('Global Settings') }}</h4>
						<form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('administration/theme_option/update') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="row">

								<div class="col-md-12">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Banner Image') }}</label>						
									<input type="file" class="dropify" name="home_banner_image" data-allowed-file-extensions="jpg jpeg png" data-default-file="{{ get_option('home_banner_image') != '' ? asset('public/uploads/media/'.get_option('home_banner_image')) : theme_asset('assets/images/header-bg.jpg') }}">
								  </div>
								</div>
								
								<div class="col-md-12">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Sub Page Banner') }}</label>						
									<input type="file" class="dropify" name="sub_banner_image" data-allowed-file-extensions="jpg jpeg png" data-default-file="{{ get_option('sub_banner_image') != '' ? asset('public/uploads/media/'.get_option('sub_banner_image')) : theme_asset('assets/images/header-bg.jpg') }}">
								  </div>
								</div>
								
								<div class="col-md-8">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Promo Video URL') }}</label>						
									<input type="text" class="form-control" name="promo_video_url" value="{{ get_option('promo_video_url') }}">
								  </div>
								</div>

								<div class="col-md-4">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Contact Email Address') }}</label>				
									<input type="text" class="form-control" name="contact_email" value="{{ get_option('contact_email') }}">
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
			</div><!--End Tab-->
				
			<div id="home_page" class="tab-pane">
				<div class="card">
				    <div class="card-body">
					    <h4 class="mb-4 header-title panel-title">{{ _lang('Home Page') }}</h4>

					    <ul class="nav nav-tabs">
						    @foreach($language_list as $language)
							 	<li class="nav-item">
							 	   <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#home-language-{{ $loop->index + 1 }}">{{ $language }}</a>
							  	</li>
						    @endforeach
						</ul>
						<br>

					    <form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('administration/theme_option/update') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="tab-content">

								@foreach($language_list as $language)
								<div class="tab-pane container {{ $loop->first ? 'active' : '' }}" id="home-language-{{ $loop->index + 1 }}">
									<div class="row">
										
										<div class="col-md-12">
										  <div class="form-group">
											<label class="control-label">{{ _lang('Hero Title') }}</label>						
											<input type="text" class="form-control" name="hero_title[{{$language}}]" value="{{ get_array_option('hero_title',$language) }}">
										  </div>
										</div>
										
										<div class="col-md-12">
										  <div class="form-group">
											<label class="control-label">{{ _lang('Hero Sub Title') }}</label>						
											<input type="text" class="form-control" name="hero_sub_title[{{$language}}]" value="{{ get_array_option('hero_sub_title',$language) }}">
										  </div>
										</div>


										<div class="col-md-12">
										  <div class="form-group">
											<label class="control-label">{{ _lang('Website Copyright') }}</label>				
											<input type="text" class="form-control" name="website_copyright[{{$language}}]" value="{{ get_array_option('website_copyright',$language) }}">
										  </div>
										</div>

										<div class="col-md-12">
										  <div class="form-group">
											<button type="submit" class="btn btn-primary">{{ _lang('Save Settings') }}</button>
										  </div>
										</div>
									</div> <!--End Row-->
								</div>

								@endforeach
							</div>
					    </form>
					</div>
				</div>
			</div> <!--End Tab-->

			<div id="about_page" class="tab-pane fade">
				<div class="card">
				    <div class="card-body">
					    <h4 class="mb-4 header-title panel-title">{{ _lang('About Page') }}</h4>

					     <ul class="nav nav-tabs">
						    @foreach($language_list as $language)
							 	<li class="nav-item">
							 	   <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#about-language-{{ $loop->index + 1 }}">{{ $language }}</a>
							  	</li>
						    @endforeach
						</ul>
						<br>

						<form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('administration/theme_option/update') }}" enctype="multipart/form-data">
							{{ csrf_field() }}

							<div class="tab-content">

								@foreach($language_list as $language)
								<div class="tab-pane container {{ $loop->first ? 'active' : '' }}" id="about-language-{{ $loop->index + 1 }}">
									<div class="row">

										<div class="col-md-12">
										  <div class="form-group">
											<label class="control-label">{{ _lang('About Content') }}</label>						
											<textarea class="form-control summernote" rows="10" name="about_content[{{$language}}]">{{ get_array_option('about_content',$language) }}</textarea>
										  </div>
										</div>

										<div class="col-md-12">
										  <div class="form-group">
											<label class="control-label">{{ _lang('About Image') }}</label>						
											<input type="file" class="dropify" name="about_image[{{$language}}]" data-allowed-file-extensions="jpg jpeg png" data-default-file="{{ get_array_option('about_image',$language) != '' ? asset('public/uploads/media/'.get_array_option('about_image',$language)) : theme_asset('assets/images/about.png') }}">
										  </div>
										</div>
									
										<div class="col-md-12">
										  <div class="form-group">
											<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
										  </div>
										</div>
									</div>		
								</div>
								@endforeach
							</div>	<!--End Tab Content-->					
						</form>
				    </div>
				 </div>
			</div><!--End Tab-->

			<div id="terms_page" class="tab-pane fade">
				<div class="card">
				    <div class="card-body">
						<h4 class="mb-4 header-title panel-title">{{ _lang('Terms & Condition Page') }}</h4>

						  <ul class="nav nav-tabs">
						    @foreach($language_list as $language)
							 	<li class="nav-item">
							 	   <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#terms-language-{{ $loop->index + 1 }}">{{ $language }}</a>
							  	</li>
						    @endforeach
						</ul>
						<br>

						<form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('administration/theme_option/update') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="tab-content">

								@foreach($language_list as $language)
								<div class="tab-pane container {{ $loop->first ? 'active' : '' }}" id="terms-language-{{ $loop->index + 1 }}">
									<div class="row">
										
										<div class="col-md-12">
										  <div class="form-group">
											<label class="control-label">{{ _lang('Terms & Condition Content') }}</label>						
											<textarea class="form-control summernote" rows="10" name="terms_condition_content">{{ get_option('terms_condition_content') }}</textarea>
										  </div>
										</div>
									
										<div class="col-md-12">
										  <div class="form-group">
											<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
										  </div>
										</div>
									</div>	<!--End Row-->	
								</div>
								@endforeach
							</div>				
						</form>
				    </div>
				 </div>
			</div><!--End Tab-->
			 
			
			<div id="seo" class="tab-pane fade">
				<div class="card">
				    <div class="card-body">
					<h4 class="mb-4 header-title panel-title">{{ _lang('SEO Settings') }}</h4>
						<form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('administration/theme_option/update') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="row">
								<div class="col-md-12">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Website Title') }}</label>						
									<input type="text" class="form-control" name="website_title" value="{{ get_option('website_title','ElitKit') }}">
								  </div>
								</div>
								
								<div class="col-md-12">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Meta Keywords') }}</label>						
									<input type="text" class="form-control" name="meta_keywords" value="{{ get_option('meta_keywords') }}">
								  </div>
								</div>
								
								<div class="col-md-12">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Meta Description') }}</label>						
									<textarea class="form-control" name="meta_description">{{ get_option('meta_description') }}</textarea>
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
			</div><!--End Tab-->		

			<div id="social_links" class="tab-pane fade">
				<div class="card">
				    <div class="card-body">
					<h4 class="mb-4 header-title panel-title">{{ _lang('Social Links') }}</h4>
						<form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('administration/theme_option/update') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="row">
								<div class="col-md-12">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Facebook') }}</label>						
									<input type="text" class="form-control" name="facebook_link" value="{{ get_option('facebook_link') }}">
								  </div>
								</div>

								<div class="col-md-12">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Twitter') }}</label>						
									<input type="text" class="form-control" name="twitter_link" value="{{ get_option('twitter_link') }}">
								  </div>
								</div>

								<div class="col-md-12">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Instagram') }}</label>						
									<input type="text" class="form-control" name="instagram_link" value="{{ get_option('instagram_link') }}">
								  </div>
								</div>
							
								<div class="col-md-12">
								  <div class="form-group">
									<label class="control-label">{{ _lang('Linkedin') }}</label>						
									<input type="text" class="form-control" name="linkedin_link" value="{{ get_option('linkedin_link') }}">
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
			</div><!--End Tab-->		


			<div id="custom_css" class="tab-pane fade">
				<div class="card">
				    <div class="card-body">
					<h4 class="mb-4 header-title panel-title">{{ _lang('Custom CSS') }}</h4>
						<form method="post" class="appsvan-submit params-panel" autocomplete="off" action="{{ url('administration/theme_option/update') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="row">
								
								<div class="col-md-12">
								  <div class="form-group">
									<label class="control-label">{{ _lang('CSS Code') }}</label>						
									<textarea class="form-control" rows="10" name="custom_css_code">{{ get_option('custom_css_code') }}</textarea>
									<span>{{ _lang('Write Your CSS Code without style tag') }}</span>
								  </div>
								</div>
							
								<div class="col-md-12">
								  <div class="form-group">
									<button type="submit" class="btn btn-primary">{{ _lang('Save CSS') }}</button>
								  </div>
								</div>
							</div>						
						</form>
				    </div>
				 </div>
			</div><!--End Tab-->			

		</div>
	</div>
</div>
@endsection