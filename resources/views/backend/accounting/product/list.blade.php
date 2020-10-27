@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    <a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Product') }}" href="{{ route('products.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
	    <a class="btn btn-dark btn-xs" href="{{ route('products.import') }}"><i class="ti-import"></i> {{ _lang('Import') }}</a>

		<div class="card mt-2">
			<span class="panel-title d-none">{{ _lang('List Product') }}</span>
			
			
			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
					  <tr>
							<th>{{ _lang('Product') }}</th>
							<th class="text-right">{{ _lang('Product Cost') }}</th>
							<th class="text-right">{{ _lang('Product Price') }}</th>
							<th>{{ _lang('Product Unit') }}</th>
							<th class="text-center">{{ _lang('Availabel Stock') }}</th>
							<th>{{ _lang('Tax Method') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					  </tr>
					</thead>
					<tbody>
						
					  @php $currency = currency(); @endphp
					  @foreach($items as $item)
					  <tr id="row_{{ $item->id }}">
							<td class='item_id'>{{ $item->item_name }}</td>
							<td class='product_cost text-right'>{{ decimalPlace($item->product->product_cost, $currency) }}</td>
							<td class='product_price text-right'>{{ decimalPlace($item->product->product_price, $currency) }}</td>
							<td class='product_unit'>{{ $item->product->product_unit }}</td>
							<td class='tax_method text-center'>{{ $item->product_stock->quantity }}</td>
							<td class='tax_method'>{{ ucwords($item->product->tax_method) }}</td>
							<td class="text-center">
								<form action="{{action('ProductController@destroy', $item['id'])}}" method="post">
								<a href="{{action('ProductController@edit', $item['id'])}}" data-title="{{ _lang('Update Product') }}" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
								<a href="{{action('ProductController@show', $item['id'])}}" data-title="{{ _lang('View Product') }}" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>
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


