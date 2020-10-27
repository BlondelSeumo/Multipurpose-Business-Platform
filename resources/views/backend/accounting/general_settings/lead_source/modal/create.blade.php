<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('lead_sources.store') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	
    <div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('Title') }}</label>						
			<input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
		</div>
	</div>

	<div class="col-md-12">
	    <div class="form-group">
	        <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
		    <button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
	    </div>
	</div>
</form>
