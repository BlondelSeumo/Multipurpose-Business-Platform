<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('project_milestones.store') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	
    <div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('Title') }}</label>						
			<input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
		</div>
	</div>

	<input type="hidden" name="project_id" value="{{ $_GET['project_id'] }}">

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('Description') }}</label>						
			<textarea class="form-control" name="description">{{ old('description') }}</textarea>
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('Due Date') }}</label>						
			<input type="text" class="form-control datepicker" name="due_date" value="{{ old('due_date') }}" required>
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('Status') }}</label>						
			<select class="form-control auto-select" name="status" data-selected="{{ old('status') }}" required>
				<option value="">{{ _lang('Select One') }}</option>
				<option value="not_started">{{ _lang('Not Started') }}</option>
				<option value="in_progress">{{ _lang('In Progress') }}</option>
				<option value="cancelled">{{ _lang('Cancelled') }}</option>
				<option value="completed">{{ _lang('Completed') }}</option>
			</select>
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('Cost').' '.currency() }}</label>						
			<input type="text" class="form-control float-field" name="cost" value="{{ old('cost') }}">
		</div>
	</div>

	
	<div class="col-md-12">
	    <div class="form-group">
	        <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
		    <button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
	    </div>
	</div>
</form>
