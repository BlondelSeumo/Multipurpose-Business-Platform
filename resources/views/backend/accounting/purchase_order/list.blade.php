@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    <a class="btn btn-primary btn-xs" href="{{ route('purchase_orders.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="panel-title d-none">{{ _lang('List Purchase Order') }}</span>

			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
						<tr>
							<th>{{ _lang('Order Date') }}</th>
							<th>{{ _lang('Supplier') }}</th>
							<th class="text-center">{{ _lang('Order Status') }}</th>
							<th class="text-right">{{ _lang('Grand Total') }}</th>
							<th class="text-right">{{ _lang('Paid') }}</th>
							<th class="text-right">{{ _lang('Due') }}</th>
							<th class="text-center">{{ _lang('Payment Status') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						</tr>
					</thead>
					<tbody>
					    @php $currency = currency(); @endphp	
					    @php $date_format = get_company_option('date_format','Y-m-d'); @endphp	

						@foreach($purchases as $purchase)
						<tr id="row_{{ $purchase->id }}">
							  <td class='order_date'>{{ date($date_format, strtotime($purchase->order_date)) }}</td>
								<td class='supplier_id'>{{ $purchase->supplier->supplier_name }}</td>
									@if($purchase->order_status == 1)
											<td class='order_status text-center'><span class="badge badge-info">{{ _lang('Ordered') }}</span></td>
									@elseif($purchase->order_status == 2)
											<td class='order_status text-center'><span class="badge badge-danger">{{ _lang('Pending') }}</span></td>
									@elseif($purchase->order_status == 3)
											<td class='order_status text-center'><span class="badge badge-success">{{ _lang('Received') }}</span></td>
									@endif
								<td class='grand_total text-right'>{{ decimalPlace($purchase->grand_total, $currency) }}</td>	
								<td class='paid text-right'>{{ decimalPlace($purchase->paid, $currency) }}</td>	
								<td class='due text-right'>{{ $purchase->grand_total > $purchase->paid ? decimalPlace(($purchase->grand_total - $purchase->paid),$currency) : decimalPlace(0, $currency) }}</td>	
								<td class='payment_status text-center'>@if($purchase->payment_status == 0) <span class="badge badge-danger">{{ _lang('Due') }}</span> @else <span class="badge badge-success">{{ _lang('Paid') }}</span> @endif</td>	
								<td class="text-center">
									
									<div class="dropdown">
										<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}
										<i class="fa fa-angle-down"></i></button>
										<div class="dropdown-menu">
											<a href="{{ action('PurchaseController@edit', $purchase->id) }}" class="dropdown-item"><i class="fas fa-edit"></i> {{ _lang('Edit') }}</a></li>
											<a href="{{ action('PurchaseController@show', $purchase->id) }}" class="dropdown-item"><i class="fas fa-eye"></i> {{ _lang('View') }}</a>
											<a href="{{ url('purchase_orders/create_payment/'.$purchase->id) }}" data-title="{{ _lang('Make Payment') }}" class="dropdown-item ajax-modal"><i class="fas fa-credit-card"></i> {{ _lang('Make Payment') }}</a>
											<a target="_blank" href="{{ url('purchase_orders/view_payment/'.$purchase->id) }}" data-title="{{ _lang('Payment History') }}" data-fullscreen="true" class="dropdown-item"><i class="fas fa-credit-card"></i> {{ _lang('Payment History') }}</a>
											
											<form action="{{action('PurchaseController@destroy', $purchase['id'])}}" method="post">									
												{{ csrf_field() }}
												<input name="_method" type="hidden" value="DELETE">
												<button class="button-link btn-remove" type="submit"><i class="fas fa-recycle"></i> {{ _lang('Delete') }}</button>
											</form>
											
										</div>
									</div>
									
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


