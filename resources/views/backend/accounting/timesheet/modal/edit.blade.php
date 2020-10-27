<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ action('TimeSheetController@update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">				
	<div class="row p-2">
		<div class="col-md-6">
			<div class="form-group">
			   <label class="control-label">{{ _lang('Start Time') }}</label>						
			   <input type="text" class="form-control datetimepicker" name="start_time" value="{{ $timesheet->start_time }}" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
			   <label class="control-label">{{ _lang('End Time') }}</label>						
			   <input type="text" class="form-control datetimepicker" name="end_time" value="{{ $timesheet->end_time }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('User') }}</label>						
				<select class="form-control select2" name="user_id"  required>
					<option value="">{{ _lang('Select One') }}</option>
					{{ create_option('users','id','name',$timesheet->user_id, array('company_id='=>company_id())) }}
				</select>
			</div>
		</div>

		<input type="hidden" name="project_id" value="{{ $timesheet->project_id }}" required>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Task') }}</label>						
				<select class="form-control select2" name="task_id"  required>
					<option value="">{{ _lang('Select One') }}</option>
					{{ create_option('tasks','id','title',$timesheet->task_id, array('project_id=' => $timesheet->project_id,'AND company_id='=>company_id())) }}
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
			   <label class="control-label">{{ _lang('Note') }}</label>						
			   <textarea class="form-control" name="note">{{ $timesheet->note }}</textarea>
			</div>
		</div>

		
		<div class="form-group">
		    <div class="col-md-12">
			    <button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
		    </div>
		</div>
	</div>
</form>

