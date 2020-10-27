<table class="table table-bordered">
	<tr><td>{{ _lang('Trans Date') }}</td><td>{{ date("d M, Y",strtotime($transaction->trans_date)) }}</td></tr>
	<tr><td>{{ _lang('Account') }}</td><td>{{ $transaction->account->account_title }}</td></tr>
	<tr><td>{{ _lang('Category') }}</td><td>{{ isset($transaction->expense_type->name) ? $transaction->expense_type->name : _lang('Transfer') }}</td></tr>
	<tr><td>{{ _lang('Amount') }}</td><td>{{ decimalPlace($transaction->amount, currency($transaction->account->account_currency), $company_currency[$transaction->company_id]) }}</td></tr>
	<tr><td>{{ _lang('Base Amount') }}</td><td>{{ decimalPlace($transaction->amount, currency($company_currency[$transaction->company_id]), $company_currency[$transaction->company_id]) }}</td></tr>
	<tr><td>{{ _lang('Payment Method') }}</td><td>{{ $transaction->payment_method->name }}</td></tr>
	<tr><td>{{ _lang('Reference') }}</td><td>{{ $transaction->reference }}</td></tr>
	<tr>
		<td>{{ _lang('Attachment') }}</td>
			<td>
				@if($transaction->attachment != "")
				 <a href="{{ asset('public/uploads/transactions/'.$transaction->attachment) }}" target="_blank" class="btn btn-primary">{{ _lang('View Attachment') }}</a>
				@else
					<label class="badge badge-warning">
					<strong>{{ _lang('No Atachment Availabel !') }}</strong>
					</label>
				@endif
			</td>
	</tr>
	<tr><td>{{ _lang('Note') }}</td><td>{{ $transaction->note }}</td></tr>
</table>


