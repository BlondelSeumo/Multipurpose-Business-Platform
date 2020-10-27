@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Payment Method') }}" href="{{route('payment_methods.create')}}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('List Payment Method') }}</span>

			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
					  <tr>
						<th>{{ _lang('Name') }}</th>
						<th class="action-col">{{ _lang('Action') }}</th>
					  </tr>
					</thead>
					<tbody>
					  
					  @foreach($paymentmethods as $paymentmethod)
					  <tr id="row_{{ $paymentmethod->id }}">
						<td class='name'>{{ $paymentmethod->name }}</td>	
						<td class="text-center">
						  <form action="{{ action('PaymentMethodController@destroy', $paymentmethod['id']) }}" method="post">
							<a href="{{ action('PaymentMethodController@edit', $paymentmethod['id']) }}" data-title="{{ _lang('Update Payment Method') }}" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
							<a href="{{ action('PaymentMethodController@show', $paymentmethod['id']) }}" data-title="{{ _lang('View Payment Method') }}" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>
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


