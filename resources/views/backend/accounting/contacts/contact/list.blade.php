@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    <a class="btn btn-primary btn-xs" href="{{ route('contacts.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>

	    <a class="btn btn-dark btn-xs" href="{{ route('contacts.import') }}"><i class="ti-import"></i> {{ _lang('Import') }}</a>
			
		<div class="card mt-2">
			<span class="panel-title d-none">{{ _lang('List Contact') }}</span>
			<div class="card-body">
			<table id="contacts-table" class="table table-bordered">
			<thead>
			  <tr>
				<th>{{ _lang('Image') }}</th>
				<th>{{ _lang('Contact Name') }}</th>
				<th>{{ _lang('Profile Type') }}</th>
				<th>{{ _lang('Email') }}</th>
				<th>{{ _lang('Phone') }}</th>
				<th>{{ _lang('Group') }}</th>
				<th class="text-center">{{ _lang('Action') }}</th>
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
<script src="{{ asset('public/backend/assets/js/ajax-datatable/contacts.js') }}"></script>
@endsection


