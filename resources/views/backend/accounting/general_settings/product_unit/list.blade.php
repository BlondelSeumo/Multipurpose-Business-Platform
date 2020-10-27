@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Product Unit') }}" href="{{ route('product_units.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('List Product Unit') }}</span>

			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
					  <tr>
						<th>{{ _lang('Unit Name') }}</th>
						<th class="text-center">{{ _lang('Action') }}</th>
					  </tr>
					</thead>
					<tbody> 
					  @foreach($productunits as $productunit)
					  <tr id="row_{{ $productunit->id }}">
						<td class='unit_name'>{{ $productunit->unit_name }}</td>
						<td class="text-center">
						  <form action="{{action('ProductUnitController@destroy', $productunit['id'])}}" method="post">
							<a href="{{action('ProductUnitController@edit', $productunit['id'])}}" data-title="{{ _lang('Update Product Unit') }}" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
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


