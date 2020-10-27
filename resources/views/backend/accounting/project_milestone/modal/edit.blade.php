<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ action('ProjectMilestoneController@update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">				
	
	<div class="col-md-12">
		<div class="form-group">
		   <label class="control-label">{{ _lang('Title') }}</label>						
		   <input type="text" class="form-control" name="title" value="{{ $projectmilestone->title }}" required>
		</div>
	</div>

	<input type="hidden" name="project_id" value="{{ $projectmilestone->project_id }}">

	<div class="col-md-12">
		<div class="form-group">
		   <label class="control-label">{{ _lang('Description') }}</label>						
		   <textarea class="form-control" name="description">{{ $projectmilestone->description }}</textarea>
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
		   <label class="control-label">{{ _lang('Due Date') }}</label>						
		   <input type="text" class="form-control datepicker" name="due_date" value="{{ $projectmilestone->due_date }}" required>
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">{{ _lang('Status') }}</label>						
			<select class="form-control auto-select" name="status" data-selected="{{ $projectmilestone->status }}" required>
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
		   <input type="text" class="form-control float-field" name="cost" value="{{ $projectmilestone->cost }}">
		</div>
	</div>

	
	<div class="form-group">
	    <div class="col-md-12">
		    <button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
	    </div>
	</div>
</form>

