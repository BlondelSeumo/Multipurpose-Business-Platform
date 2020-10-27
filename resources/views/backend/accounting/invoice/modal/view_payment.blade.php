<style>
#main_modal .modal-lg {
    max-width: 800px;
}
</style>

@php $currency = currency() @endphp
@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	

<!--payment History table -->
<div class="table-responsive">
	<table id="order-table" class="table table-bordered">
		<thead>
			<tr>
				<th>{{ _lang('Date') }}</th>
				<th>{{ _lang('Account') }}</th>
				<th>{{ _lang('Income Type') }}</th>
				<th class="text-right">{{ _lang('Amount') }}</th>
				<th>{{ _lang('Payment Method') }}</th>
			</tr>
		</thead>

		<tbody>
			@foreach($transactions as $transaction)
				<tr id="transaction-{{ $transaction->id }}">
					<td>{{ date($date_format, strtotime($transaction->trans_date)) }}</td>
					<td>{{ $transaction->account->account_title.' - '.$transaction->account->account_currency }}</td>
					<td>{{ $transaction->income_type->name }}</td>
					<td class="text-right">{{ decimalPlace($transaction->amount, currency($transaction->account->account_currency)) }}</td>
					<td>{{ $transaction->payment_method->name }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
<!--End Order table -->
