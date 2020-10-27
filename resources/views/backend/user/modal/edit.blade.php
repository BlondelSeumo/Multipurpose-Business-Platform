<form method="post" class="ajax-submit" autocomplete="off" action="{{action('UserController@update', $id)}}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">				
	<div class="row p-2">
		<div class="col-md-12">
		 <div class="form-group">
			<label class="control-label">{{ _lang('Business Name') }}</label>						
			<input type="text" class="form-control" name="business_name" value="{{ $user->company->business_name }}" required>
		 </div>
		</div>
		
		<div class="col-md-6">
		 <div class="form-group">
			<label class="control-label">{{ _lang('Name') }}</label>						
			<input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
		 </div>
		</div>

		<div class="col-md-6">
		 <div class="form-group">
			<label class="control-label">{{ _lang('Email') }}</label>						
			<input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
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
			<input type="password" class="form-control" name="password_confirmation">
		 </div>
		</div>
		
		<div class="col-md-12">
			<div class="alert alert-info">
				{{ _lang('Package will be reset if you change the package') }}
			</div>
		</div>

		<div class="col-md-6">
		  	<div class="form-group">
				<label class="control-label">{{ _lang('Package') }}</label>		
				<select class="form-control select2 auto-select" data-selected="{{ $user->company->package_id }}" id="package_id" name="package_id" required>
					<option value="">{{ _lang('Select Package') }}</option>
					{{ create_option('packages','id','package_name') }}
				</select>
			</div>
		</div>
		  
		<div class="col-md-6">
		  <div class="form-group">
			<label class="control-label">{{ _lang('Package Type') }}</label>						
			<select class="form-control auto-select" data-selected="{{ $user->company->package_type }}" id="package_type" name="package_type" required>
				<option value="monthly">{{ _lang('Monthly') }}</option>
				<option value="yearly">{{ _lang('Yearly') }}</option>
			</select>
		  </div>
		</div>

		
		<div class="col-md-6">
		  <div class="form-group">
			<label class="control-label">{{ _lang('Membership Type') }}</label>						
				<select class="form-control select2 auto-select" data-selected="{{ $user->company->membership_type }}" name="membership_type" id="membership_type" required>
			   		<option value="trial">{{ _lang('Trial') }}</option>
			   		<option value="member">{{ _lang('Paid Member') }}</option>
			 	</select>
		  </div>
		</div>
		
		<div class="col-md-6">
		  <div class="form-group">
			<label class="control-label">{{ _lang('Status') }}</label>						
			<select class="form-control select2 auto-select" data-selected="{{ $user->company->status }}" id="status" name="status" required>
			  	<option value="1">{{ _lang('Active') }}</option>
			  	<option value="0">{{ _lang('Inactive') }}</option>
			</select>
		  </div>
		</div>
		
		
		<div class="col-md-12">
		 <div class="form-group">
			<label class="control-label">{{ _lang('Profile Picture') }} ( 300 X 300 {{ _lang('for better view') }} )</label>						
			<input type="file" class="dropify" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="{{ $user->profile_picture != "" ? asset('public/uploads/profile/'.$user->profile_picture) : '' }}" >
		 </div>
		</div>

					
		<div class="form-group">
		  <div class="col-md-12">
			<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
		  </div>
		</div>
	</div>	
</form>
