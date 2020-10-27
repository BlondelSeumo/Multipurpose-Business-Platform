@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<span class="d-none panel-title">{{ _lang('Update Staff') }}</span>

			<div class="card-body">
				<form method="post" class="validate" autocomplete="off" action="{{ action('StaffController@update', $id) }}" enctype="multipart/form-data">
					
					<div class="row">
						<div class="col-md-6">
							{{ csrf_field()}}
							<input name="_method" type="hidden" value="PATCH">				
							
							<div class="col-md-12">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Name') }}</label>						
								<input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
							 </div>
							</div>

							<div class="col-md-12">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Email') }}</label>						
								<input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
							 </div>
							</div>

							<div class="col-md-12">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Password') }}</label>						
								<input type="password" class="form-control" name="password" value="">
							 </div>
							</div>
							
							<div class="col-md-12">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Confirm Password') }}</label>						
								<input type="password" class="form-control" name="password_confirmation" value="">
							 </div>
							</div>
							
							<div class="col-md-12">
							  <div class="form-group">
								<label class="control-label">{{ _lang('Status') }}</label>						
								<select class="form-control select2 auto-select" data-selected="{{ $user->status }}" id="status" name="status" required>
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
									  {{ create_option('staff_roles','id','name', $user->role_id, array('company_id='=>company_id())) }}
									</select>
							  	</div>
							</div>				
							
							<div class="form-group">
							  <div class="col-md-12">
								<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
							  </div>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="col-md-12">
							 <div class="form-group">
								<label class="control-label">{{ _lang('Profile Picture') }} ( 300 X 300 {{ _lang('for better view') }} )</label>						
								<input type="file" class="dropify" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="{{ $user->profile_picture != "" ? asset('public/uploads/profile/'.$user->profile_picture) : '' }}" >
							 </div>
							</div>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>

@endsection

