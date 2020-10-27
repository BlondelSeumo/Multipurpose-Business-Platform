@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<a class="btn btn-primary btn-xs mb-2 ajax-modal" data-title="{{ _lang('Create New Lead') }}" href="{{ route('leads.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
		<a class="btn btn-dark btn-xs mb-2" href="{{ route('leads.import') }}"><i class="ti-upload"></i> {{ _lang('Imports') }}</a>
		<a class="btn btn-secondary btn-xs mb-2" href="{{ url('leads/kanban') }}"><i class="ti-layout-column3"></i> {{ _lang('Kanban View') }}</a>
		<div class="card mt-2">
		    
			<span class="panel-title d-none">{{ _lang('Lead List') }}</span>
				
			<div class="card-body">
                <div class="row">
                     <div class="col-lg-3">
                     	 <label>{{ _lang('Assigned') }}</label>
                     	 <select class="form-control select2 select-filter" name="assigned_user_id">
                             <option value="">{{ _lang('ALL') }}</option>
                             {{ create_option('users','id','name','',array('company_id=' => company_id())) }}
                     	 </select>
                     </div>	

                     <div class="col-lg-3">
                     	 <label>{{ _lang('Lead Status') }}</label>
                     	 <select class="form-control select2 select-filter" data-placeholder="{{ _lang('ALL') }}" name="lead_status_id" multiple="true">
							{{ create_option('lead_statuses','id','title','',array('company_id=' => company_id())) }}
                     	 </select>
                     </div>	

                     <div class="col-lg-3">
                     	 <label>{{ _lang('Lead Source') }}</label>
                     	 <select class="form-control select2 select-filter" name="lead_source_id">
                     	 	<option value="">{{ _lang('ALL') }}</option>
							{{ create_option('lead_sources','id','title','',array('company_id=' => company_id())) }}
                     	 </select>
                     </div>	

                      <div class="col-lg-3">
                     	 <label>{{ _lang('Country') }}</label>
                     	 <select class="form-control select2 select-filter" name="country">
                     	 	<option value="">{{ _lang('ALL') }}</option>
							{{ get_country_list() }}
                     	 </select>
                     </div>		
                </div>

                <hr>

				<table id="leads_table" class="table table-striped">
					<thead>
					  <tr>
						<th>{{ _lang('Name') }}</th>
						<th>{{ _lang('Company') }}</th>
						<th>{{ _lang('Email') }}</th>
						<th>{{ _lang('Phone') }}</th>
						<th>{{ _lang('Status') }}</th>
						<th>{{ _lang('Source') }}</th>
						<th>{{ _lang('Assigned ') }}</th>	
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
<script src="{{ asset('public/backend/assets/js/ajax-datatable/leads.js') }}"></script>
@endsection