@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    <a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Supplier') }}" href="{{ route('suppliers.create') }}"><i class="ti-plus"></i>  {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="panel-title d-none">{{ _lang('List Of Supplier') }}</span>

			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
						<tr>
							<th>{{ _lang('Supplier Name') }}</th>
							<th>{{ _lang('Company Name') }}</th>
							<th>{{ _lang('Vat Number') }}</th>
							<th>{{ _lang('Email') }}</th>
							<th>{{ _lang('Phone') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						</tr>
					</thead>
					<tbody>
						
						@foreach($suppliers as $supplier)
						<tr id="row_{{ $supplier->id }}">
							<td class='supplier_name'>{{ $supplier->supplier_name }}</td>
							<td class='company_name'>{{ $supplier->company_name }}</td>
							<td class='vat_number'>{{ $supplier->vat_number }}</td>
							<td class='email'>{{ $supplier->email }}</td>
							<td class='phone'>{{ $supplier->phone }}</td>
							<td class="text-center">
								<form action="{{action('SupplierController@destroy', $supplier['id'])}}" method="post">
								<a href="{{action('SupplierController@edit', $supplier['id'])}}" data-title="{{ _lang('Update Supplier Information') }}" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
								<a href="{{action('SupplierController@show', $supplier['id'])}}" data-title="{{ _lang('View Supplier') }}" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>
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


