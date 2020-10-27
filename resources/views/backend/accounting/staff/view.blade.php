@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<span class="d-none panel-title">{{ _lang('View Staff') }}</span>

			<div class="card-body">
			  	<table class="table table-bordered">
					<tr><td colspan="2" class="text-center"><img class="thumb-xl rounded" src="{{ $user->profile_picture != "" ? asset('public/uploads/profile/'.$user->profile_picture) : asset('public/images/avatar.png') }}"></td></tr>
					<tr><td>{{ _lang('Name') }}</td><td>{{ $user->name }}</td></tr>
					<tr><td>{{ _lang('Email') }}</td><td>{{ $user->email }}</td></tr>
					<tr><td>{{ _lang('User Type') }}</td><td>{{ $user->user_type }}</td></tr>	
					<tr><td>{{ _lang('Role') }}</td><td>{{ $user->role->name }}</td></tr>	
			  	</table>
			</div>
	  	</div>
 	</div>
</div>
@endsection


