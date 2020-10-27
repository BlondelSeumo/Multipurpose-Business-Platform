@extends('layouts.app')
@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<h5 class="card-header bg-primary text-white mt-0 panel-title">{{ _lang('My Profile') }}</h5>
			
			@php $date_format = get_option('date_format','Y-m-d'); @endphp
			
			<div class="card-body">
				<table class="table table-bordered" width="100%">
					<tbody>
						<tr class="text-center">
							<td colspan="2"><img class="thumb-xl rounded" src="{{ $profile->profile_picture != "" ? asset('public/uploads/profile/'.$profile->profile_picture) : asset('public/images/avatar.png') }}"></td>
						</tr>
						@if(Auth::user()->user_type == 'client')	
							<tr><td>{{ _lang('Profile Type') }}</td><td>{{ $profile->client->profile_type }}</td></tr>
							<tr><td>{{ _lang('Company Name') }}</td><td>{{ $profile->client->company_name }}</td></tr>
							<tr><td>{{ _lang('Contact Name') }}</td><td>{{ $profile->client->contact_name }}</td></tr>
							<tr><td>{{ _lang('Contact Email') }}</td><td>{{ $profile->client->contact_email }}</td></tr>
							<tr><td>{{ _lang('Contact Phone') }}</td><td>{{ $profile->client->contact_phone }}</td></tr>
							<tr><td>{{ _lang('Country') }}</td><td>{{ $profile->client->country }}</td></tr>
							<tr><td>{{ _lang('City') }}</td><td>{{ $profile->client->city }}</td></tr>
							<tr><td>{{ _lang('State') }}</td><td>{{ $profile->client->state }}</td></tr>
							<tr><td>{{ _lang('Zip') }}</td><td>{{ $profile->client->zip }}</td></tr>
							<tr><td>{{ _lang('Address') }}</td><td>{{ $profile->client->address }}</td></tr>
							<tr><td>{{ _lang('Facebook') }}</td><td>{{ $profile->client->facebook }}</td></tr>
							<tr><td>{{ _lang('Twitter') }}</td><td>{{ $profile->client->twitter }}</td></tr>
							<tr><td>{{ _lang('Linkedin') }}</td><td>{{ $profile->client->linkedin }}</td></tr>
							<tr><td>{{ _lang('Remarks') }}</td><td>{{ $profile->client->remarks }}</td></tr>
							<tr><td>{{ _lang('Group') }}</td><td>{{ $profile->client->group->name }}</td></tr>
						@else
							<tr>
								<td>{{ _lang('Name') }}</td>
								<td>{{ $profile->name }}</td>
							</tr>
							<tr>
								<td>{{ _lang('Email') }}</td>
								<td>{{ $profile->email }}</td>
							</tr>
							@if(\Auth::user()->user_type == 'user')					
								<tr>
									<td>{{ _lang('Valid To') }}</td>
									<td>{{ $profile->valid_to != '' ? date($date_format, strtotime($profile->valid_to)) : '' }}</td>
								</tr>
							@endif
						@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection