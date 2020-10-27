<link href="{{ asset('public/backend/plugins/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet">

<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ action('LeadStatusController@update', $id) }}">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">				
	
	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Title') }}</label>						
		<input type="text" class="form-control" name="title" value="{{ $leadstatus->title }}" required>
	 </div>
	</div>

	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Color') }}</label>						
		<input type="text" class="form-control colorpicker" name="color" value="{{ $leadstatus->color }}" required>
	 </div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Order') }}</label>						
		<input type="number" class="form-control" name="order" min="0" value="{{ $leadstatus->order }}" required>
	  </div>
	</div>
				
	<div class="form-group">
	  <div class="col-md-12">
		<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
	  </div>
	</div>
</form>

<script src="{{ asset('public/backend/plugins/bootstrap-colorpicker/bootstrap-colorpicker.js') }}"></script>

<script type="text/javascript">
$('.colorpicker').colorpicker();
</script>
