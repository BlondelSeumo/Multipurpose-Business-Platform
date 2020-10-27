@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
	<div class="card">
	<div class="card-body">
	  <h4 class="d-none panel-title">{{ _lang('View User') }}</h4>
	    
	   @php $date_format = get_option('date_format','Y-m-d'); @endphp	

	   <table class="table table-bordered">
			<tr><td colspan="2" class="text-center"><img class="thumb-xl rounded" src="{{ $user->profile_picture != "" ? asset('public/uploads/profile/'.$user->profile_picture) : asset('public/images/avatar.png') }}"></td></tr>
			<tr><td>{{ _lang('Business Name') }}</td><td>{{ $user->company->business_name }}</td></tr>
			<tr><td>{{ _lang('Admin Name') }}</td><td>{{ $user->name }}</td></tr>
			<tr><td>{{ _lang('Admin Email') }}</td><td>{{ $user->email }}</td></tr>
			<tr><td>{{ _lang('Status') }}</td><td>{!! $user->company->status == 1 ? clean(status(_lang('Active'), 'success')) : clean(status(_lang('In-Active'), 'danger')) !!}</td></tr>
			@if($user->user_type == 'user')
				<tr><td>{{ _lang('Package') }}</td><td>{{ $user->company->package->package_name }}({{ ucwords($user->company->package_type) }})</td></tr>	
				<tr><td>{{ _lang('Package Valid To') }}</td><td>{{ date($date_format, strtotime($user->company->valid_to)) }}</td></tr>	
		        <tr>
		        	<td>{{ _lang('Membersip Type') }}</td><td>{!! $user->company->membership_type == 'trial' ? clean(status(ucwords($user->company->membership_type), 'danger')) : clean(status(ucwords($user->company->membership_type), 'success')) !!}</td>
		        </tr>
			@endif
	    </table>

	    @if($user->user_type == 'user')
		    <table class="table table-striped">
		    	<tr>
		    		<td colspan="2" class="text-center"><b>{{ _lang('Package Details') }}</b></td>
		    	</tr>
		    	<tr>
		    		<td><b>{{ _lang('Feature') }}</b></td>
		    		<td class="text-center"><b>{{ _lang('Avaialble Limit') }}</b></td>
		    	</tr>
		    	<tr>
		    		<td>{{ _lang('Staff Limit') }}</td>
		    		<td class="text-center">{{ $user->company->staff_limit }}</td>
		    	</tr>
		    	<tr>
		    		<td>{{ _lang('Contacts Limit') }}</td>
		    		<td class="text-center">{{ $user->company->contacts_limit }}</td>
		    	</tr>
		    	<tr>
		    		<td>{{ _lang('Invoice Limit') }}</td>
		    		<td class="text-center">{{ $user->company->invoice_limit }}</td>
		    	</tr>
		    	<tr>
		    		<td>{{ _lang('Quotation Limit') }}</td>
		    		<td class="text-center">{{ $user->company->quotation_limit }}</td>
		    	</tr>
		    	<tr>
		    		<td>{{ _lang('Project Management') }}</td>
		    		<td class="text-center">{{ ucwords($user->company->project_management_module) }}</td>
		    	</tr>
		    	<tr>
		    		<td>{{ _lang('Recurring Transaction') }}</td>
		    		<td class="text-center">{{ ucwords($user->company->recurring_transaction) }}</td>
		    	</tr>
				<tr>
					<td>{{ _lang('Inventory Module') }}</td>
					<td class="text-center">{{ ucwords($user->company->inventory_module) }}</td>
				</tr>
		    	<tr>
		    		<td>{{ _lang('Live Chat') }}</td>
		    		<td class="text-center">{{ ucwords($user->company->live_chat) }}</td>
		    	</tr>
		    	<tr>
		    		<td>{{ _lang('File Manager') }}</td>
		    		<td class="text-center">{{ ucwords($user->company->file_manager) }}</td>
		    	</tr>
		    	<tr>
		    		<td>{{ _lang('Online Payment') }}</td>
		    		<td class="text-center">{{ ucwords($user->company->online_payment) }}</td>
		    	</tr>
		    </table>
	    @endif
	</div>
  </div>
 </div>
</div>
@endsection


