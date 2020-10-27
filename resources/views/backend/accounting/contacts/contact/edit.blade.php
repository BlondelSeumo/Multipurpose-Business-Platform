@extends('layouts.app')

@section('content')
<div class="row">
   <div class="col-12">
	   <form method="post" class="validate" autocomplete="off" action="{{action('ContactController@update', $id)}}" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-8">
				<div class="card">
					<span class="d-none panel-title">{{ _lang('Update Contact Info') }}</span>

					<div class="card-body">

						{{ csrf_field()}}
						<input name="_method" type="hidden" value="PATCH">							
						
						<div class="row">
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Profile Type') }}</label>						
								<select class="form-control select2" name="profile_type" required>
									<option value="Company" {{ $contact->profile_type=="Company" ? "selected" : "" }}>{{ _lang('Company') }}</option>
									<option value="Individual" {{ $contact->profile_type=="Individual" ? "selected" : "" }}>{{ _lang('Individual') }}</option>
								</select>
							  </div>
							</div>

							<div class="col-md-6">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Company Name') }}</label>						
								<input type="text" class="form-control" name="company_name" value="{{ $contact->company_name }}">
							 </div>
							</div>

							<div class="col-md-6">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Contact Name') }}</label>						
								<input type="text" class="form-control" name="contact_name" value="{{ $contact->contact_name }}" required>
							 </div>
							</div>

							<div class="col-md-6">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Contact Email') }}</label>						
								<input type="text" class="form-control" name="contact_email" value="{{ $contact->contact_email }}" required>
							 </div>
							</div>
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('VAT ID') }}</label>						
								<input type="text" class="form-control" name="vat_id" value="{{ $contact->vat_id }}">
							  </div>
							</div>

							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Reg No') }}</label>						
								<input type="text" class="form-control" name="reg_no" value="{{ $contact->reg_no }}">
							  </div>
							</div>

							<div class="col-md-6">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Contact Phone') }}</label>						
								<input type="text" class="form-control" name="contact_phone" value="{{ $contact->contact_phone }}">
							 </div>
							</div>
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Country') }}</label>						
								<select class="form-control select2" name="country">
									<option value="">{{ _lang('Select Country') }}</option>
									{{ get_country_list( $contact->country ) }}
								</select>
							  </div>
							</div>
							
							<div class="col-md-6">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Currency') }}</label>						
								<select class="form-control select2 auto-select" data-selected="{{ $contact->currency }}" name="currency" id="currency" required>
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
									{{ create_option("contact_groups","id","name",$contact->group_id,array("company_id="=>company_id())) }}
								</select>
							 </div>
							</div>

							<div class="col-md-4">
							 <div class="form-group">
								<label class="control-label">{{ _lang('City') }}</label>						
								<input type="text" class="form-control" name="city" value="{{ $contact->city }}">
							 </div>
							</div>

							<div class="col-md-4">
							 <div class="form-group">
								<label class="control-label">{{ _lang('State') }}</label>						
								<input type="text" class="form-control" name="state" value="{{ $contact->state }}">
							 </div>
							</div>

							<div class="col-md-4">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Zip') }}</label>						
								<input type="text" class="form-control" name="zip" value="{{ $contact->zip }}">
							 </div>
							</div>

							<div class="col-md-6">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Address') }}</label>						
								<textarea class="form-control" name="address">{{ $contact->address }}</textarea>
							 </div>
							</div>
							
							<div class="col-md-6">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Remarks') }}</label>						
								<textarea class="form-control" name="remarks">{{ $contact->remarks }}</textarea>
							 </div>
							</div>

							<div class="col-md-12">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Facebook') }}</label>						
								<input type="text" class="form-control" name="facebook" value="{{ $contact->facebook }}">
							 </div>
							</div>

							<div class="col-md-12">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Twitter') }}</label>						
								<input type="text" class="form-control" name="twitter" value="{{ $contact->twitter }}">
							 </div>
							</div>

							<div class="col-md-12">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Linkedin') }}</label>						
								<input type="text" class="form-control" name="linkedin" value="{{ $contact->linkedin }}">
							 </div>
							</div>

							<div class="col-md-12">
							  <div class="form-group">
								<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
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
							<label class="control-label">{{ _lang('Contact Image') }}</label>						
							<input type="file" class="form-control dropify" name="contact_image" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="{{ $contact->contact_image != "" ? asset('public/uploads/contacts/'.$contact->contact_image) : '' }}">
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


