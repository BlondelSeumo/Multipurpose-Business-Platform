@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add New Service') }}" href="{{ route('services.create') }}"><i class="ti-plus"></i>  {{ _lang('Add New') }}</a>
		<a class="btn btn-dark btn-xs" href="{{ route('services.import') }}"><i class="ti-import"></i>  {{ _lang('Import') }}</a>

		<div class="card mt-2">
			<span class="panel-title d-none">{{ _lang('List Service') }}</span>

			<div class="card-body">
			<table class="table table-bordered data-table">
			<thead>
			  <tr>
					<th>{{ _lang('Service') }}</th>
					<th class="text-right">{{ _lang('Cost') }}</th>
					<th>{{ _lang('Tax Method') }}</th>
					<th class="text-center">{{ _lang('Action') }}</th>
			  </tr>
			</thead>
			<tbody>
				
				@php $currency = currency(); @endphp
			  @foreach($items as $item)
			  <tr id="row_{{ $item->id }}">
					<td class='item_id'>{{ $item->item_name }}</td>
					<td class='cost text-right'>{{ decimalPlace($item->service->cost, $currency) }}</td>
					<td class='tax_method'>{{ ucwords($item->service->tax_method) }}</td>
					<td class="text-center">
						<form action="{{action('ServiceController@destroy', $item['id'])}}" method="post">
						<a href="{{action('ServiceController@edit', $item['id'])}}" data-title="{{ _lang('Update Service') }}" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
						<a href="{{action('ServiceController@show', $item['id'])}}" data-title="{{ _lang('View Service') }}" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>
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


