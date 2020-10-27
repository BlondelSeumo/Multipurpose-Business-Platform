<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('staffs.store') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	
	<div class="col-12">
		<div class="row">
			<div class="col-md-12">
			  <div class="form-group">
				<label class="control-label">{{ _lang('Name') }}</label>						
				<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
			  </div>
			</div>

			<div class="col-md-6">
			  <div class="form-group">
				<label class="control-label">{{ _lang('Email') }}</label>						
				<input type="email" class="form-control" name="email" value="{{ old('email') }}">
			  </div>
			</div>

			<div class="col-md-6">
			  <div class="form-group">
				<label class="control-label">{{ _lang('Password') }}</label>						
				<input type="password" class="form-control" name="password">
			  </div>
			</div>
			
			<div class="col-md-6">
			 <div class="form-group">
				<label class="control-label">{{ _lang('Confirm Password') }}</label>						
				<input type="password" class="form-control" name="password_confirmation" required>
			 </div>
			</div>
			
			<div class="col-md-6">
			  	<div class="form-group">
					<label class="control-label">{{ _lang('Status') }}</label>						
					<select class="form-control select2" id="status" name="status" required>
					  <option value="1">{{ _lang('Active') }}</option>
					  <option value="0">{{ _lang('Inactive') }}</option>
					</select>
			  	</div>
			</div>

			<div class="col-md-12">
			  	<div class="form-group">
					<label class="control-label">{{ _lang('Role') }}</label>						
					<select class="form-control select2" name="role_id" required>
					  <option value="">{{ _lang('Select Role') }}</option>
					  {{ create_option('staff_roles','id','name', old('role_id'), array('company_id='=>company_id())) }}
					</select>
			  	</div>
			</div>
			
			<div class="col-md-12">
			 <div class="form-group">
				<label class="control-label">{{ _lang('Profile Picture') }} ( 300 X 300 {{ _lang('for better view') }} )</label>	
				<input type="file" class="dropify" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="">
			 </div>
			</div>
						
			<div class="col-md-12">
			  <div class="form-group">
				<button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
				<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
			  </div>
			</div>
		</div>
	</div>
</form>