@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header bg-primary text-white">
				<span class="panel-title">{{ _lang('Update Lead') }}</span>
			</div>
			<div class="card-body">
				<form method="post" class="validate" autocomplete="off" action="{{ action('LeadController@update', $id) }}" enctype="multipart/form-data">
					{{ csrf_field()}}
					<input name="_method" type="hidden" value="PATCH">				
					<div class="row">
						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('Name') }}</label>						
							   <input type="text" class="form-control" name="name" value="{{ $lead->name }}" required>
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('Company Name') }}</label>	
							   <input type="text" class="form-control" name="company_name" value="{{ $lead->company_name }}">
						    </div>
						</div>

						<div class="col-md-6">
					        <div class="form-group">
						        <label class="control-label">{{ _lang('Email') }}</label>		
						        <input type="text" class="form-control" name="email" value="{{ $lead->email }}">
					        </div>
					    </div>

						<div class="col-md-6">
						    <div class="form-group">
						    	<a href="{{ route('lead_statuses.create') }}" data-reload="false" data-title="{{ _lang('New Lead Status') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
							    <label class="control-label">{{ _lang('Lead Status') }}</label>	
							    <select class="form-control select2-ajax" data-value="id" data-display="title" data-table="lead_statuses" data-where="1" name="lead_status_id"  required>
					                <option value="">{{ _lang('Select One') }}</option>
									{{ create_option('lead_statuses', 'id', 'title', $lead->lead_status_id,array("company_id="=>company_id())) }}
							    </select>
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
						    	<a href="{{ route('lead_sources.create') }}" data-reload="false" data-title="{{ _lang('New Lead Source') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
							    <label class="control-label">{{ _lang('Lead Source') }}</label>	
							    <select class="form-control select2-ajax" data-value="id" data-display="title" data-table="lead_sources" data-where="1" name="lead_source_id"  required>
					                <option value="">{{ _lang('Select One') }}</option>
									{{ create_option('lead_sources', 'id', 'title', $lead->lead_source_id,array("company_id="=>company_id())) }}
							    </select>
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							    <label class="control-label">{{ _lang('Assigned ') }}</label>	
							    <select class="form-control select2" name="assigned_user_id"  required>
					                <option value="">{{ _lang('Select One') }}</option>
									{{ create_option('users','id','name',$lead->assigned_user_id, array("company_id="=>company_id())) }}
							    </select>
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('Contact Date') }}</label>
							   <input type="text" class="form-control datepicker" name="contact_date" value="{{ $lead->contact_date }}" required>
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('Phone') }}</label>
							   <input type="text" class="form-control" name="phone" value="{{ $lead->phone }}">
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('Website') }}</label>
							   <input type="text" class="form-control" name="website" value="{{ $lead->website }}">
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							    <label class="control-label">{{ _lang('Country') }}</label>
							    <select class="form-control select2 auto-select" data-selected="{{ $lead->country }}" name="country" >
					                <option value="">{{ _lang('Select One') }}</option>
									{{ get_country_list( $lead->country ) }}
							    </select>
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							    <label class="control-label">{{ _lang('Currency') }}</label>
							    <select class="form-control select2 auto-select" name="currency" data-selected="{{ $lead->currency }}" required>
					                <option value="">{{ _lang('Select One') }}</option>
									{{ get_currency_list() }}
							    </select>
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('Vat ID') }}</label>
							   <input type="text" class="form-control" name="vat_id" value="{{ $lead->vat_id }}">
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('Reg No') }}</label>
							   <input type="text" class="form-control" name="reg_no" value="{{ $lead->reg_no }}">
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('City') }}</label>						
							   <input type="text" class="form-control" name="city" value="{{ $lead->city }}">
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('State') }}</label>
							   <input type="text" class="form-control" name="state" value="{{ $lead->state }}">
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('Zip') }}</label>						
							   <input type="text" class="form-control" name="zip" value="{{ $lead->zip }}">
						    </div>
						</div>

						<div class="col-md-6">
						    <div class="form-group">
							   <label class="control-label">{{ _lang('Address') }}</label>
							   <textarea class="form-control" name="address">{{ $lead->address }}</textarea>
						    </div>
						</div>

						<div class="col-md-12">
						    <div class="form-group">
							    <button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
						    </div>
						</div>
					</div>	
				</form>
			</div>
		</div>
	</div>
</div>

@endsection


