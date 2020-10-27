@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    <a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Account') }}" href="{{ route('accounts.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('List Account') }}</span>

			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
					  <tr>
						<th>{{ _lang('Account Title') }}</th>
						<th>{{ _lang('Opening Date') }}</th>
						<th>{{ _lang('Account Number') }}</th>
						<th>{{ _lang('Currency') }}</th>
						<th class="text-right">{{ _lang('Current Balance') }}</th>
						<th class="action-col">{{ _lang('Action') }}</th>
					  </tr>
					</thead>
					<tbody>
					  @php $currency = currency(); @endphp
					  @php $date_format = get_company_option('date_format','Y-m-d'); @endphp	

					  @foreach($accounts as $account)
					  <tr id="row_{{ $account->id }}">
						<td class='account_title'>{{ $account->account_title }}</td>
						<td class='opening_date'>{{ date($date_format, strtotime($account->opening_date)) }}</td>
						<td class='account_number'>{{ $account->account_number }}</td>
						<td class='account_currency'>{{ $account->account_currency }}</td>
						<td class='opening_balance text-right'>{{ decimalPlace($account->balance, currency($account->account_currency)) }}</td>
						<td class="text-center">
						  <form action="{{ action('AccountController@destroy', $account->id) }}" method="post">
							<a href="{{ action('AccountController@edit', $account->id) }}" data-title="{{ _lang('Update Account') }}" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
							<a href="{{ action('AccountController@show', $account->id) }}" data-title="{{ _lang('View Account') }}" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>
							{{ csrf_field() }}
							<input name="_method" type="hidden" value="DELETE">
							<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i></button>
						  </form>
						</td>
					  </tr>
					  @endforeach
					</tbody>
			    </table>
			</div>
		</div>
	</div>
</div>

@endsection


