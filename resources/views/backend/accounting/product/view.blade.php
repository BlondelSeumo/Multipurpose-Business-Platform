@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
	<div class="card">
	<span class="panel-title d-none">{{ _lang('View Product') }}</span>

	<div class="card-body">
	    <table class="table table-bordered">
			<tr><td>{{ _lang('Product Name') }}</td><td>{{ $item->item_name }}</td></tr>
			<tr><td>{{ _lang('Supplier') }}</td><td>{{ $item->product->supplier->supplier_name }}</td></tr>
			<tr><td>{{ _lang('Product Cost') }}</td><td>{{ decimalPlace($item->product->product_cost, currency()) }}</td></tr>
			<tr><td>{{ _lang('Product Price') }}</td><td>{{ decimalPlace($item->product->product_price, currency()) }}</td></tr>
			<tr><td>{{ _lang('Product Unit') }}</td><td>{{ $item->product->product_unit }}</td></tr>
			<tr><td>{{ _lang('Availabel Quantity') }}</td><td>{{ $item->product_stock->quantity.' '.$item->product->product_unit }}</td></tr>
			<tr><td>{{ _lang('Tax Method') }}</td><td>{{ ucwords($item->product->tax_method) }}</td></tr>
			<tr><td>{{ _lang('Tax') }}</td><td>{{ isset($item->product->tax) ? $item->product->tax->tax_name : '' }}</td></tr>
			<tr><td>{{ _lang('Description') }}</td><td>{{ $item->product->description }}</td></tr>	
	    </table>
	</div>
  </div>
 </div>
</div>
@endsection


