@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    <a class="btn btn-primary btn-xs" href="{{ route('purchase_returns.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('List Purchase Return') }}</span>

			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
						<tr>
							<th>{{ _lang('Return Date') }}</th>
							<th>{{ _lang('Supplier') }}</th>
							<th class="text-right">{{ _lang('Grand Total') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						</tr>
					</thead>
					<tbody>
						@php $currency = currency(); @endphp	
						@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	

						@foreach($purchase_returns as $purchase)
						<tr id="row_{{ $purchase->id }}">
						  <td class='order_date'>{{ date($date_format, strtotime($purchase->return_date)) }}</td>
							<td class='supplier_id'>{{ $purchase->supplier->supplier_name }}</td>
							<td class='grand_total text-right'>{{ decimalPlace($purchase->grand_total, $currency) }}</td>	
							<td class="text-center">
								
								<div class="dropdown">
									<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}
									<i class="fa fa-angle-down"></i></button>
									<div class="dropdown-menu">
										<a class="dropdown-item" href="{{ action('PurchaseReturnController@edit', $purchase->id) }}"><i class="fas fa-edit"></i> {{ _lang('Edit') }}</a>
										<a href="{{ action('PurchaseReturnController@show', $purchase->id) }}" data-title="{{ _lang('View Purchase Return') }}" data-fullscreen="true" class="dropdown-item ajax-modal"><i class="fas fa-eye"></i> {{ _lang('View') }}</a>
										
										<form action="{{ action('PurchaseReturnController@destroy', $purchase['id']) }}" method="post">									
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


