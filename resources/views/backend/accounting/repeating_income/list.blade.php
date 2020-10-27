@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    <a class="btn btn-primary btn-xs" data-title="{{ _lang('Add Repeating Income') }}" href="{{ route('repeating_income.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('List Repeating Income') }}</span>

			<div class="card-body">
				<table class="table table-bordered" id="repeating-income-table">
					<thead>
						<tr>
						<th>{{ _lang('Date') }}</th>
						<th>{{ _lang('Account') }}</th>
						<th>{{ _lang('Income Type') }}</th>
						<th class="text-right">{{ _lang('Amount') }}</th>
						<th>{{ _lang('Payer') }}</th>
						<th>{{ _lang('Status') }}</th>
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
<script src="{{ asset('public/backend/assets/js/ajax-datatable/repeating-income.js') }}"></script>
@endsection


