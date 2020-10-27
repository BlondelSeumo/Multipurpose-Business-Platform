@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
		    <span class="d-none panel-title">{{ _lang('View Purchase Retrurn') }}</span>
			<div class="card-body">
				@php $currency = currency() @endphp
				@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	
				
				<table class="table table-bordered">
					<tr><td>{{ _lang('Return Date') }}</td><td>{{ date($date_format, strtotime($purchase->return_date)) }}</td></tr>
					<tr><td>{{ _lang('Supplier') }}</td><td>{{ isset($purchase->supplier) ? $purchase->supplier->supplier_name : '' }}</td></tr>
					<tr><td>{{ _lang('Tax') }}</td><td>{{ decimalPlace($purchase->tax_amount, $currency) }}</td></tr>
					<tr><td>{{ _lang('Product Total') }}</td><td>{{ decimalPlace($purchase->product_total, $currency) }}</td></tr>
					<tr><td>{{ _lang('Grand Total') }}</td><td>{{ decimalPlace($purchase->grand_total, $currency) }}</td></tr>	
					<tr><td>{{ _lang('Attachemnt') }}</td><td>@if($purchase->attachemnt != "") <a class="btn btn-success btn-xs" target="_blank" href="{{ asset('public/uploads/attachments/'.$purchase->attachemnt) }}">{{ _lang('View') }}</a> @else <span class="badge badge-danger">{{ _lang('Not Availabel !') }}</span>@endif</td></tr>
					<tr><td>{{ _lang('Note') }}</td><td>{{ $purchase->note }}</td></tr>	
				</table>
			
			
				<!--Order table -->
				<div class="table-responsive">
					<table id="order-table" class="table table-bordered">
						<thead>
							<tr>
								<th>{{ _lang('Name') }}</th>
								<th class="text-center wp-100">{{ _lang('Quantity') }}</th>
								<th class="text-right">{{ _lang('Unit Cost') }}</th>
								<th class="text-right wp-100">{{ _lang('Discount') }}</th>
								<th class="text-right">{{ _lang('Tax method') }}</th>
								<th class="text-right">{{ _lang('Tax') }}</th>
								<th class="text-right">{{ _lang('Sub Total') }}</th>
							</tr>
						</thead>
		
						<tbody>
							@foreach($purchase->purchase_return_items as $item)
								<tr id="product-{{ $item->product_id }}">
									<td>
										<b>{{ $item->item->item_name }}</b><br>
										{{ $item->description }}
									</td>
									<td class="text-center quantity">{{ decimalPlace($item->quantity, $currency) }}</td>
									<td class="text-right unit-cost">{{ decimalPlace($item->unit_cost, $currency) }}</td>
									<td class="text-right discount">{{ decimalPlace($item->discount, $currency) }}</td>
									<td class="text-right tax-method">{{ strtoupper($item->item->product->tax_method) }}</td>
									<td class="text-right tax">{{ decimalPlace($item->tax_amount, $currency) }}</td>
									<td class="text-right sub-total">{{ decimalPlace($item->sub_total, $currency) }}</td>
							    </tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<!--End Order table -->
			</div>
		</div>
    </div>
</div>
@endsection


