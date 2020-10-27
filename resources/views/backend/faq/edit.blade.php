@extends('layouts.app')

@section('content')
@php $language_list = get_language_list(); @endphp
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="panel-title d-none">{{ _lang('Update FAQ') }}</div>
			<div class="card-body">
				<div class="col-md-12">
					<ul class="nav nav-tabs">
					    @foreach($language_list as $language)
						 	<li class="nav-item">
						 	   <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#faq-{{ $loop->index + 1 }}">{{ $language }}</a>
						  	</li>
					    @endforeach
					</ul>
					<br>
				</div>

				<form method="post" class="validate" autocomplete="off" action="{{action('FaqController@update', $id)}}" enctype="multipart/form-data">
					{{ csrf_field()}}
					<input name="_method" type="hidden" value="PATCH">
					<div class="tab-content">

						@foreach($language_list as $language)
						<div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="faq-{{ $loop->index + 1 }}">			
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">{{ _lang('Question') }}</label>						
									<input type="text" class="form-control" name="question[{{ $language }}]" value="{{ get_array_data($faq->question, $language) }}" required>
								</div>
							</div>

							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">{{ _lang('Answer') }}</label>						
									<textarea class="form-control summernote" name="answer[{{ $language }}]">{{ get_array_data($faq->answer, $language) }}</textarea>
								</div>
							</div>

				
							<div class="col-md-12">
								<div class="form-group">
									<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
								</div>
							</div>
						</div>
						@endforeach
					</div>	
				</form>
			</div>
		</div>
	</div>
</div>
@endsection


