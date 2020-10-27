@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
	<div class="card">
	<div class="d-none panel-title">{{ _lang('View Package') }}</div>

	<div class="card-body">
	    <table class="table table-bordered">
			<tr>
				<td>{{ _lang('Package Name') }}</td>
				<td colspan="2">{{ $package->package_name }}</td>
			</tr>
			
			<tr>
				<td><b>{{ _lang('Features') }}</b></td>
				<td><b>{{ _lang('Monthly') }}</b></td>
				<td><b>{{ _lang('Yearly') }}</b></td>
			</tr>
			
			<tr>
				<td>{{ _lang('Staff Limit') }}</td>
				<td>{{ unserialize($package->staff_limit)['monthly'] }}</td>
				<td>{{ unserialize($package->staff_limit)['yearly'] }}</td>
			</tr>
			
			<tr>
				<td>{{ _lang('Contacts Limit') }}</td>
				<td>{{ unserialize($package->contacts_limit)['monthly'] }}</td>
				<td>{{ unserialize($package->contacts_limit)['yearly'] }}</td>
			</tr>
			
			<tr>
				<td>{{ _lang('Invoice Limit') }}</td>
				<td>{{ unserialize($package->invoice_limit)['monthly'] }}</td>
				<td>{{ unserialize($package->invoice_limit)['yearly'] }}</td>
			</tr>
			
			<tr>
				<td>{{ _lang('Quotation Limit') }}</td>
				<td>{{ unserialize($package->quotation_limit)['monthly'] }}</td>
				<td>{{ unserialize($package->quotation_limit)['yearly'] }}</td>
			</tr>

			<tr>
				<td>{{ _lang('Project Management') }}</td>
				<td>{{ ucwords(unserialize($package->project_management_module)['monthly']) }}</td>
				<td>{{ ucwords(unserialize($package->project_management_module)['yearly']) }}</td>
			</tr>

			<tr>
				<td>{{ _lang('Recurring Transaction') }}</td>
				<td>{{ ucwords(unserialize($package->recurring_transaction)['monthly']) }}</td>
				<td>{{ ucwords(unserialize($package->recurring_transaction)['yearly']) }}</td>
			</tr>
			
			<tr>
				<td>{{ _lang('Inventory Module') }}</td>
				<td>{{ ucwords(unserialize($package->inventory_module)['monthly']) }}</td>
				<td>{{ ucwords(unserialize($package->inventory_module)['yearly']) }}</td>
			</tr>
			
			<tr>
				<td>{{ _lang('Live Chat') }}</td>
				<td>{{ ucwords(unserialize($package->live_chat)['monthly']) }}</td>
				<td>{{ ucwords(unserialize($package->live_chat)['yearly']) }}</td>
			</tr>
			
			<tr>
				<td>{{ _lang('File Manager') }}</td>
				<td>{{ ucwords(unserialize($package->file_manager)['monthly']) }}</td>
				<td>{{ ucwords(unserialize($package->file_manager)['yearly']) }}</td>
			</tr>
			
			<tr>
				<td>{{ _lang('Online Payment') }}</td>
				<td>{{ ucwords(unserialize($package->online_payment)['monthly']) }}</td>
				<td>{{ ucwords(unserialize($package->online_payment)['yearly']) }}</td>
			</tr>
			
			<tr>
				<td>{{ _lang('Cost') }}</td>
				<td><b>{{ decimalPlace($package->cost_per_month, currency()).' / '._lang('Month') }}</b></td>
				<td><b>{{ decimalPlace($package->cost_per_year, currency()).' / '._lang('Year') }}</b></td>
			</tr>	
		</table>
	</div>
  </div>
 </div>
</div>
@endsection


