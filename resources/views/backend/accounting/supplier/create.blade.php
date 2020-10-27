@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="d-none panel-title">{{ _lang('Add Supplier') }}</div>

			<div class="card-body">
			  	<form method="post" class="validate" autocomplete="off" action="{{url('suppliers')}}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-md-6">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Supplier Name') }}</label>						
							<input type="text" class="form-control" name="supplier_name" value="{{ old('supplier_name') }}" required>
						  </div>
						</div>

						<div class="col-md-6">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Company Name') }}</label>						
							<input type="text" class="form-control" name="company_name" value="{{ old('company_name') }}">
						  </div>
						</div>

						<div class="col-md-6">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Vat Number') }}</label>						
							<input type="text" class="form-control" name="vat_number" value="{{ old('vat_number') }}">
						  </div>
						</div>

						<div class="col-md-6">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Email') }}</label>						
							<input type="text" class="form-control" name="email" value="{{ old('email') }}" required>
						  </div>
						</div>

						<div class="col-md-6">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Phone') }}</label>						
							<input type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
						  </div>
						</div>

						<div class="col-md-6">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Address') }}</label>						
							<input type="text" class="form-control" name="address" value="{{ old('address') }}">
						  </div>
						</div>

						<div class="col-md-6">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Country') }}</label>						
							<select class="form-control select2" name="country">
								<option value="">{{ _lang('Select Country') }}</option>
								{{ get_country_list(old('country')) }}
							</select>
						  </div>
						</div>

						<div class="col-md-6">
						  <div class="form-group">
							<label class="control-label">{{ _lang('City') }}</label>						
							<input type="text" class="form-control" name="city" value="{{ old('city') }}">
						  </div>
						</div>

						<div class="col-md-6">
						  <div class="form-group">
							<label class="control-label">{{ _lang('State') }}</label>						
							<input type="text" class="form-control" name="state" value="{{ old('state') }}">
						  </div>
						</div>

						<div class="col-md-6">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Postal Code') }}</label>						
							<input type="text" class="form-control" name="postal_code" value="{{ old('postal_code') }}">
						  </div>
						</div>

						
						<div class="form-group">
						  <div class="col-md-12">
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


