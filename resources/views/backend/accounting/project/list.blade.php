@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">

		<div class="card mt-2">
			<span class="panel-title d-none">{{ _lang('Project List') }}</span>		
			<div class="card-body">
				<div class="row">
					 <div class="col-lg-6 mb-2">
                     	 <a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add New Project') }}" href="{{ route('projects.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
                     </div>	
                     <div class="col-lg-3 mb-2">
                     	 <select class="form-control select2 select-filter" name="client_id">
                             <option value="">{{ _lang('All Customer') }}</option>
                             {{ create_option('contacts','id','contact_name','',array('company_id=' => company_id())) }}
                     	 </select>
                     </div>	

                     <div class="col-lg-3">
                     	 <select class="form-control select2 select-filter" data-placeholder="{{ _lang('All Status') }}" name="status" 
                     	 multiple="true">
                     	 	<option value="not_started">{{ _lang('Not Started') }}</option>
							<option value="in_progress">{{ _lang('In Progress') }}</option>
							<option value="on_hold">{{ _lang('On Hold') }}</option>
							<option value="cancelled">{{ _lang('Cancelled') }}</option>
							<option value="completed">{{ _lang('Completed') }}</option>		
                     	 </select>
                     </div>		
                </div>

                <hr>


				<table id="projects_table" class="table table-bordered">
					<thead>
					    <tr>
							<th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Client') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th>{{ _lang('Start Date') }}</th>
							<th>{{ _lang('End Date') }}</th>
							<th>{{ _lang('Project Members') }}</th>
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
<script src="{{ asset('public/backend/assets/js/ajax-datatable/projects.js') }}"></script>
@endsection