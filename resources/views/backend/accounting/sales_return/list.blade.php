@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    <a class="btn btn-primary btn-xs" href="{{ route('sales_returns.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('List Sales Return') }}</span>
            @php $currency = currency(); @endphp	
            @php $base_currency = base_currency(); @endphp	
            @php $date_format = get_company_option('date_format','Y-m-d'); @endphp	

			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
						<tr>
							<th>{{ _lang('Return Date') }}</th>
							<th>{{ _lang('Customer') }}</th>
							<th class="text-right">{{ _lang('Grand Total') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						</tr>
					</thead>
					<tbody>
							
						@foreach($sales_returns as $sales)
						<tr id="row_{{ $sales->id }}">
								<td class='order_date'>{{ date($date_format, strtotime($sales->return_date)) }}</td>
								<td class='customer_id'>{{ $sales->customer->contact_name }}</td>
								<td class='grand_total text-right'>
									<span>{{ decimalPlace($sales->grand_total, $currency) }}</span><br>
									@if($base_currency != $sales->customer->currency)
										<span>{{ decimalPlace($sales->converted_total, currency($sales->customer->currency)) }}</span>
								    @endif
								</td>	
								<td class="text-center">
									
									<div class="dropdown">
										<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}
										<i class="fa fa-angle-down"></i></button>
										<div class="dropdown-menu">
											<a class="dropdown-item" href="{{ action('SalesReturnController@edit', $sales->id) }}"><i class="fas fa-edit"></i> {{ _lang('Edit') }}</a>
											<a href="{{ action('SalesReturnController@show', $sales->id) }}" data-title="{{ _lang('View Sales Return') }}" data-fullscreen="true" class="ajax-modal dropdown-item"><i class="fas fa-eye"></i> {{ _lang('View') }}</a>
											
											<form action="{{action('SalesReturnController@destroy', $sales->id)}}" method="post">									
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


