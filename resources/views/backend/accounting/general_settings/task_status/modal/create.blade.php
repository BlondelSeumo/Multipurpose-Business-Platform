<link href="{{ asset('public/backend/plugins/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet">

<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('task_statuses.store') }}">
	{{ csrf_field() }}
	
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Title') }}</label>						
		<input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
	  </div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Color') }}</label>						
		<input type="text" class="form-control colorpicker" name="color" value="{{ old('color') }}" required>
	  </div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Order') }}</label>						
		<input type="number" class="form-control" name="order" min="0" value="{{ old('order') }}" required>
	  </div>
	</div>
				
	<div class="col-md-12">
	  <div class="form-group">
	    <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
		<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
	  </div>
	</div>
</form>

<script src="{{ asset('public/backend/plugins/bootstrap-colorpicker/bootstrap-colorpicker.js') }}"></script>

<script type="text/javascript">
$('.colorpicker').colorpicker();
</script>
