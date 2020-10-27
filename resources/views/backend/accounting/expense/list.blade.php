@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    <a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Expense') }}" href="{{ route('expense.create') }}"><i class="ti-plus"></i>  {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('List Expense') }}</span>
			
			<div class="card-body">
				<table id="expense-table" class="table table-bordered">
						<thead>
							<tr>
								<th>{{ _lang('Date') }}</th>
								<th>{{ _lang('Account') }}</th>
								<th>{{ _lang('Expense Type') }}</th>
								<th class="text-right">{{ _lang('Amount') }}</th>
								<th>{{ _lang('Method') }}</th>
								<th class="action-col">{{ _lang('Action') }}</th>
							</tr>
						</thead>
						<tbody>
				
						</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/ajax-datatable/expense.js') }}"></script>
@endsection
