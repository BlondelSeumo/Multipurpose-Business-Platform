@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="panel-title d-none">{{ _lang('Edit Translation') }}</div>

			<div class="card-body">
			  
			  @if( @ini_get('max_input_vars') < 2000 )
				<div class="alert alert-danger">
					<span>You must need to set <b>max_input_vars = 2000</b> for updating language</span>
				</div>						
			  @endif
			  
			  <form method="post" class="validate" autocomplete="off" action="{{ action('LanguageController@update', $id) }}">
				{{ csrf_field() }}
				<input name="_method" type="hidden" value="PATCH">
				<div class="row">
					@foreach($language as $key => $lang)
					<div class="col-md-6">
					  <div class="form-group">
						<label class="control-label">{{ ucwords($key) }}</label>						
						<input type="text" class="form-control" name="language[{{ str_replace(' ','_',$key) }}]" value="{{ $lang }}" required>
					  </div>
					</div>
					@endforeach
					
					<div class="col-md-12">
					  <div class="form-group">
						<button type="submit" class="btn btn-primary">{{ _lang('Save Translation') }}</button>
					  </div>
					</div>
				</div>
			  </form>
			</div>
	  	</div>
 	</div>
</div>
@endsection
