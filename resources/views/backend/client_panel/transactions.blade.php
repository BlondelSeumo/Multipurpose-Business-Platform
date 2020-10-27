@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="card">
			<span class="d-none panel-title">{{ _lang('Transaction List') }}</span>

			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
						<tr>
							<th>{{ _lang('Date') }}</th>
							<th>{{ _lang('Account') }}</th>
							<th>{{ _lang('Category') }}</th>
							<th class="text-right">{{ _lang('Amount') }}</th>
							<th>{{ _lang('Payment Method') }}</th>
							<th class="action-col">{{ _lang('View Details') }}</th>
						</tr>
					</thead>
					<tbody>
						@php $date_format = get_option('date_format','Y-m-d'); @endphp
						@foreach($transactions as $transaction)
						 <tr>
							<td>{{ date($date_format,strtotime($transaction->trans_date)) }}</td>
							<td>{{ isset($transaction->account) ? $transaction->account->account_title : '' }}</td>
							<td>{{ isset($transaction->expense_type->name) ? $transaction->expense_type->name : _lang('Transfer') }}</td>
							<td class="text-right">{{ decimalPlace($transaction->amount, currency($transaction->account->account_currency), $transaction->payer->currency) }}</td>
							<td>{{ isset($transaction->payment_method) ? $transaction->payment_method->name : '' }}</td>
							<td class="text-center"><a href="{{ url('client/view_transaction/'.$transaction->id) }}" data-title="{{ _lang('View Transaction Details') }}" class="btn btn-primary btn-xs ajax-modal">{{ _lang('View') }}</a></td>
						</tr>
						@endforeach
					</tbody>
			  </table>
			</div>
		</div>
	</div>
</div>

@endsection