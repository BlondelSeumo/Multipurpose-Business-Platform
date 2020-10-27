@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Create Task') }}" href="{{ route('tasks.create') }}">
			<i class="ti-plus"></i> {{ _lang('Add New') }}
		</a>
		<a class="btn btn-secondary btn-xs" href="{{ url('tasks/kanban') }}"><i class="ti-layout-column3"></i> {{ _lang('Kanban View') }}</a>
		<div class="card mt-2">

			<span class="panel-title d-none">{{ _lang('Task List') }}</span>
				
			<div class="card-body">
				<div class="row">
					<div class="col-lg-3">
                     	 <label>{{ _lang('Project') }}</label>
                     	 <select class="form-control select2 select-filter" name="project_id">
                     	 	<option value="">{{ _lang('All Project') }}</option>
							{{ create_option('projects','id','name','',array('company_id=' => company_id())) }}
                     	 </select>
                     </div>	

                     <div class="col-lg-3">
                     	 <label>{{ _lang('Assigned') }}</label>
                     	 <select class="form-control select2 select-filter" name="assigned_user_id">
                             <option value="">{{ _lang('All User') }}</option>
                             {{ create_option('users','id','name','',array('company_id=' => company_id())) }}
                     	 </select>
                     </div>	

                     <div class="col-lg-3">
                     	 <label>{{ _lang('Status') }}</label>
                     	 <select class="form-control select2 select-filter" data-placeholder="{{ _lang('All Status') }}" name="task_status_id" multiple="true">
							{{ create_option('task_statuses','id','title','',array('company_id=' => company_id())) }}
                     	 </select>
                     </div>	

                     <div class="col-lg-3">
                     	 <label>{{ _lang('Deadline') }}</label>
                     	 <input type="text" class="form-control select-filter" id="date_range" autocomplete="off" name="date_range">
                     </div>	
	
                </div>

                <hr>

				<table id="tasks_table" class="table table-bordered">
					<thead>
					    <tr>
						    <th>{{ _lang('Title') }}</th>
							<th>{{ _lang('Project') }}</th>
							<th>{{ _lang('Priority') }}</th>
							<th>{{ _lang('Task Status') }}</th>
							<th>{{ _lang('Assigned User') }}</th>
							<th>{{ _lang('Start Date') }}</th>
							<th>{{ _lang('End Date') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/ajax-datatable/tasks.js') }}"></script>
@endsection