<form method="post" class="ajax-submit" autocomplete="off" action="{{action('SupplierController@update', $id)}}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">				
	
	<div class="col-12">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
				<label class="control-label">{{ _lang('Supplier Name') }}</label>						
				<input type="text" class="form-control" name="supplier_name" value="{{ $supplier->supplier_name }}" required>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
				<label class="control-label">{{ _lang('Company Name') }}</label>						
				<input type="text" class="form-control" name="company_name" value="{{ $supplier->company_name }}">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
				<label class="control-label">{{ _lang('Vat Number') }}</label>						
				<input type="text" class="form-control" name="vat_number" value="{{ $supplier->vat_number }}">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
				<label class="control-label">{{ _lang('Email') }}</label>						
				<input type="text" class="form-control" name="email" value="{{ $supplier->email }}" required>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
				<label class="control-label">{{ _lang('Phone') }}</label>						
				<input type="text" class="form-control" name="phone" value="{{ $supplier->phone }}" required>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
				<label class="control-label">{{ _lang('Address') }}</label>						
				<input type="text" class="form-control" name="address" value="{{ $supplier->address }}">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label">{{ _lang('Country') }}</label>						
					<select class="form-control select2" name="country">
						<option value="">{{ _lang('Select Country') }}</option>
						{{ get_country_list($supplier->country) }}
					</select>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
				<label class="control-label">{{ _lang('City') }}</label>						
				<input type="text" class="form-control" name="city" value="{{ $supplier->city }}">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
				<label class="control-label">{{ _lang('State') }}</label>						
				<input type="text" class="form-control" name="state" value="{{ $supplier->state }}">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
				<label class="control-label">{{ _lang('Postal Code') }}</label>						
				<input type="text" class="form-control" name="postal_code" value="{{ $supplier->postal_code }}">
				</div>
			</div>

						
			<div class="col-md-12">
			  <div class="form-group">
				<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
			  </div>
			</div>
		</div>
	</div>
</form>

