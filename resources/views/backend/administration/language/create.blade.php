@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-md-4">
		<div class="card">		
			<span class="panel-title d-none">{{ _lang('Add New Language') }}</span>

			<div class="card-body">
			  <form method="post" class="validate" autocomplete="off" action="{{ url('languages') }}" enctype="multipart/form-data">
				{{ csrf_field() }}
				<div class="row">
				    <div class="col-md-12">
						<div class="form-group">
							<label class="control-label">{{ _lang('Language Name') }}</label>						
							<input type="text" class="form-control" name="language_name" value="{{ old('language_name') }}" required>
						</div>
					</div>
					
					<div class="col-md-12">
						<div class="form-group">
							<button type="submit" class="btn btn-primary">{{ _lang('Create Language') }}</button>
						</div>
					</div>
				</div>
			  </form>
			</div>
	  	</div>
 	</div>
</div>
@endsection


