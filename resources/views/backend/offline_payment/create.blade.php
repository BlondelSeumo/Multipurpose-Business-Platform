@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-6">
	<div class="card">

	<div class="card-body">
		<h4 class="d-none panel-title">{{ _lang('Offline Payment') }}</h4>
		<form method="post" id="offline-payments" class="validate" autocomplete="off" action="{{ url('offline_payment/store') }}">
		    <div class="row">
				<div class="col-md-12">
					  {{ csrf_field() }}
					  
					  <div class="form-group">
						<label class="control-label">{{ _lang('User') }}</label>						
						<select class="form-control select2" id="user" name="user" required>
							<option value="">{{ _lang('Select User') }}</option>
							@foreach(\App\User::where('user_type','user')->get() as $user)
								<option value="{{ $user->id }}" data-package="{{ $user->company->package_id }}" data-type="{{ $user->company->package_type }}">{{ $user->name.' ('.$user->email.')' }}</option>
						    @endforeach
						</select>  
					  </div>
					  
					  <div class="form-group">
						<label class="control-label">{{ _lang('Package') }}</label>						
						<select class="form-control" id="package" name="package" required>
							<option value="">{{ _lang('Select Package') }}</option>
							{{ create_option('packages','id','package_name') }}
						</select>  
					  </div>	

					  <div class="form-group">
							<label class="control-label">{{ _lang('Package Type') }}</label>	
							<select class="form-control" id="package_type" name="package_type" required>
								<option value="">{{ _lang('Select Package Type') }}</option>
								<option value="monthly">{{ _lang('Monthly Pack') }}</option>
								<option value="yearly">{{ _lang('Yearly Pack') }}</option> 
							</select>  					  
					  </div>
						
					  <div class="form-group">
						<button type="submit" class="btn btn-primary">{{ _lang('Make Payment') }}</button>
					  </div>
				</div>
            </div>			
		</form>
	</div>
  </div>
 </div>
</div>
@endsection