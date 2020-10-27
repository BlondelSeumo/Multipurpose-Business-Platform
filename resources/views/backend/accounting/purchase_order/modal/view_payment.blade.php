<style>
#main_modal .modal-lg {
    max-width: 800px;
}
</style>
<div class="card">
	<div class="card-body">
		@php $currency = currency() @endphp
		@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	

		<!--Payment History table -->
		<div class="table-responsive">
			<table class="table table-bordered data-table">
				<thead>
					<tr>
						<th>{{ _lang('Date') }}</th>
						<th>{{ _lang('Account') }}</th>
						<th>{{ _lang('Expense Type') }}</th>
						<th class="text-right">{{ _lang('Amount') }}</th>
						<th>{{ _lang('Payment Method') }}</th>
						<th>{{ _lang('Reference') }}</th>
						<th>{{ _lang('Note') }}</th>
					</tr>
				</thead>

				<tbody>
					@foreach($transactions as $transaction)
						<tr id="transaction-{{ $transaction->id }}">
							<td>{{ date($date_format, strtotime($transaction->trans_date)) }}</td>
							<td>{{ $transaction->account->account_title }}</td>
							<td>{{ $transaction->expense_type->name }}</td>
							<td class="text-right">{{ $transaction->account->account_currency.' ('.currency($transaction->account->account_currency).') '.decimalPlace($transaction->amount) }}</td>
							<td>{{ $transaction->payment_method->name }}</td>
							<td>{{ $transaction->reference }}</td>
							<td>{{ $transaction->note }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		<!--End Order table -->
	</div>
</div>