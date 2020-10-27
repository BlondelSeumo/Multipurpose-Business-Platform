@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header bg-primary text-white">
				<span class="panel-title">{{ _lang('Create Task') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" id="task" class="validate" autocomplete="off" action="{{ route('tasks.store') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Title') }}</label>						
								<input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Description') }}</label>						
								<textarea class="form-control" name="description">{{ old('description') }}</textarea>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Project') }}</label>						
								<select class="form-control select2" name="project_id" id="project_id" required>
									<option value="">{{ _lang('Select One') }}</option>
									{{ create_option('projects', 'id', 'name', isset($_GET['project_id']) ? $_GET['project_id'] :old('project_id'), array('company_id=' => company_id())) }}
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Milestone') }}</label>						
								<select class="form-control select2" name="milestone_id" id="milestone_id">
									<option value="">-</option>
									@if(isset($_GET['project_id']))
										{{ create_option('project_milestones','id','title','', array('project_id=' => $_GET['project_id'])) }}
									@endif
								</select>
							</div>
						</div>

						
						<div class="col-md-6">
							<div class="form-group">
								<a href="{{ route('task_statuses.create') }}" data-reload="false" data-title="{{ _lang('New Task Status') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
								<label class="control-label">{{ _lang('Task Status') }}</label>						
								<select class="form-control select2-ajax" data-value="id" data-display="title" data-table="task_statuses" data-where="1" name="task_status_id" required>
									<option value="">{{ _lang('Select One') }}</option>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Priority') }}</label>						
								<select class="form-control select2" name="priority" required>
									<option value="">{{ _lang('Select One') }}</option>
									<option value="low">{{ _lang('Low') }}</option>
									<option value="medium">{{ _lang('Medium') }}</option>
									<option value="high">{{ _lang('High') }}</option>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Assigned User') }}</label>						
								<select class="form-control select2" name="assigned_user_id">
									<option value="">{{ _lang('Select One') }}</option>
									{{ create_option('users','id','name',old('assigned_user_id'),array('company_id=' => company_id())) }}
								</select>
							</div>
						</div>


						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Start Date') }}</label>						
								<input type="text" class="form-control datepicker" name="start_date" value="{{ old('start_date') }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('End Date') }}</label>						
								<input type="text" class="form-control datepicker" name="end_date" value="{{ old('end_date') }}">
							</div>
						</div>

						<input type="hidden" name="related_to" value="projects">
						
						<div class="col-md-12">
						    <div class="form-group">
						        <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
							    <button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
						    </div>
						</div>
					</div>			
			    </form>
			</div>
		</div>
    </div>
</div>
@endsection


