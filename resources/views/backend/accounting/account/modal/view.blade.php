@php $date_format = get_company_option('date_format','Y-m-d'); @endphp
<div class="card">
	<div class="card-body">
		<table class="table table-bordered">
			<tr><td>{{ _lang('Account Title') }}</td><td>{{ $account->account_title }}</td></tr>
			<tr><td>{{ _lang('Opening Date') }}</td><td>{{ date($date_format, strtotime($account->opening_date)) }}</td></tr>
			<tr><td>{{ _lang('Account Number') }}</td><td>{{ $account->account_number }}</td></tr>
			<tr><td>{{ _lang('Account Currency') }}</td><td>{{ $account->account_currency }}</td></tr>
			<tr><td>{{ _lang('Opening Balance') }}</td><td>{{ decimalPlace($account->opening_balance, currency($account->account_currency)) }}</td></tr>
			<tr><td>{{ _lang('Note') }}</td><td>{{ $account->note }}</td></tr>
		</table>
	</div>
</div>
