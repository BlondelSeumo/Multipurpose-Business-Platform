@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<h5 class="card-header bg-primary text-white mt-0 panel-title">{{ _lang('Change Password') }}</h5>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<form action="{{ url('profile/update_password') }}" class="form-horizontal form-groups-bordered validate" enctype="multipart/form-data" method="post" accept-charset="utf-8">
							@csrf
							<div class="form-group">
								<label class="control-label">{{ _lang('Old Password') }}</label>
								<input type="password" class="form-control" name="oldpassword" required>
							</div>
							<div class="form-group">
								<label class="control-label">{{ _lang('New Password') }}</label>
								<input type="password" class="form-control" name="password" required>
							</div>
							<div class="form-group">
								<label class="control-label">{{ _lang('Confirm Password') }}</label>
								<input type="password" class="form-control" id="password-confirm" name="password_confirmation" required>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary">{{ _lang('Update Password') }}</button>
							</div>
						</form>
					</div>
                </div>				
			</div>
		</div>
	</div>
</div>
@endsection

