@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    <a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Income/Expense Type') }}" href="{{ route('chart_of_accounts.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('List Income and Expense Type') }}</span>

			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
					  <tr>
						<th>{{ _lang('Name') }}</th>
						<th>{{ _lang('Type') }}</th>
						<th class="action-col">{{ _lang('Action') }}</th>
					  </tr>
					</thead>
					<tbody>
					  
					  @foreach($chartofaccounts as $chartofaccount)
					  <tr id="row_{{ $chartofaccount->id }}">
						<td class='name'>{{ $chartofaccount->name }}</td>
						<td class='type'>{{ _dlang(ucwords($chartofaccount->type)) }}</td>	
						<td class="text-center">
						  <form action="{{ action('ChartOfAccountController@destroy', $chartofaccount['id']) }}" method="post">
							<a href="{{ action('ChartOfAccountController@edit', $chartofaccount['id']) }}" data-title="{{ _lang('Update Income/Expense Type') }}" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
							<a href="{{ action('ChartOfAccountController@show', $chartofaccount['id']) }}" data-title="{{ _lang('View Income/Expense Type') }}" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>
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


