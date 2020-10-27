@extends('layouts.app')

@section('content')
<div class="row">
<div class="col-12">
<form method="post" class="validate" autocomplete="off" action="{{ route('leads.convert_to_customer',$lead->id) }}" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-8">
		<div class="card">
		<span class="d-none panel-title">{{ _lang('Add New Contact') }}</span>

		<div class="card-body">
			{{ csrf_field() }}

			<div class="row">
				<div class="col-md-6">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Profile Type') }}</label>						
					<select class="form-control select2" name="profile_type" required>
						<option value="Individual">{{ _lang('Individual') }}</option>
						<option value="Company">{{ _lang('Company') }}</option>
					</select>
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
					<label class="control-label">{{ _lang('Contact Name') }}</label>						
					<input type="text" class="form-control" name="contact_name" value="{{ $lead->name }}" required>
				  </div>
				</div>

				<div class="col-md-6">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Contact Email') }}</label>						
					<input type="text" class="form-control" name="contact_email" value="{{ $lead->email }}" required>
				  </div>
				</div>
				
				<div class="col-md-6">
				  <div class="form-group">
					<label class="control-label">{{ _lang('VAT ID') }}</label>						
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
					<label class="control-label">{{ _lang('Contact Phone') }}</label>						
					<input type="text" class="form-control" name="contact_phone" value="{{ $lead->phone }}">
				  </div>
				</div>

				<div class="col-md-6">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Country') }}</label>						
					<select class="form-control select2" name="country">
					    <option value="">{{ _lang('Select Country') }}</option>
						{{ get_country_list( $lead->country ) }}
					</select>
				  </div>
				</div>
				
				<div class="col-md-6">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Currency') }}</label>						
					<select class="form-control select2 auto-select" data-selected="{{ $lead->currency }}" name="currency" id="currency" required>
					   {{ get_currency_list() }}
					</select>
				  </div>
				</div>
				
				<div class="col-md-6">
				  <div class="form-group">
					<a href="{{ route('contact_groups.create') }}" data-reload="false" data-title="{{ _lang('Add Contact Group') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
					<label class="control-label">{{ _lang('Group') }}</label>						
					<select class="form-control select2-ajax" data-value="id" data-display="name" data-table="contact_groups" data-where="1" name="group_id" required>
						<option value="">{{ _lang('- Select Group -') }}</option>
					</select>
				 </div>
				</div>

				<div class="col-md-4">
				  <div class="form-group">
					<label class="control-label">{{ _lang('City') }}</label>						
					<input type="text" class="form-control" name="city" value="{{ $lead->city }}">
				  </div>
				</div>

				<div class="col-md-4">
				  <div class="form-group">
					<label class="control-label">{{ _lang('State') }}</label>						
					<input type="text" class="form-control" name="state" value="{{ $lead->state }}">
				  </div>
				</div>

				<div class="col-md-4">
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
				
				<div class="col-md-6">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Remarks') }}</label>						
					<textarea class="form-control" name="remarks">{{ old('remarks') }}</textarea>
				  </div>
				</div>

				<div class="col-md-12">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Facebook') }}</label>						
					<input type="text" class="form-control" name="facebook" value="{{ old('facebook') }}">
				  </div>
				</div>

				<div class="col-md-12">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Twitter') }}</label>						
					<input type="text" class="form-control" name="twitter" value="{{ old('twitter') }}">
				  </div>
				</div>

				<div class="col-md-12">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Linkedin') }}</label>						
					<input type="text" class="form-control" name="linkedin" value="{{ old('linkedin') }}">
				  </div>
				</div>

				<input type="hidden" name="lead_id" value="{{ $lead->id}}">

				<div class="col-md-12">
				  <div class="form-group">
					<button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
					<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
				  </div>
				</div>
			</div>
		</div>
	  </div>
	 </div>
	 
	 <div class="col-md-4">

	 	<div class="card">
			<h5 class="card-header bg-dark text-white mt-0 text-center">{{ _lang('Client Portal Access') }}</h5>
			<div class="card-body">
			    
			    <div class="alert alert-info">
			   	 	<span>{{ _lang('If Client have already an account associated with Contact Email then client can login to his account using existing login details') }}.</span>
			   	</div> 
			   	
			   	<div class="alert alert-info">	
			    	<span>{{ _lang('If Client do not have any previous account associated with Contact Email then client need to create a new account using that contact email') }}.</span>
			    </div>
			</div>
		</div>

		<div class="card">
			<div class="card-body">
			   <div class="col-md-12">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Contact Image') }} 300px X 300px</label>						
					<input type="file" class="form-control dropify" name="contact_image"  data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG">
				  </div>
				</div>
			</div>
		</div>

	  </div>
    </div>
 </form>
</div>
</div>
@endsection


