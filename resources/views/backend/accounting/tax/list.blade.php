@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Tax') }}" href="{{route('taxs.create')}}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('List Tax') }}</span>

			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
					  <tr>
						<th>{{ _lang('Tax Name') }}</th>
						<th>{{ _lang('Rate') }}</th>
						<th>{{ _lang('Type') }}</th>
						<th class="text-center">{{ _lang('Action') }}</th>
					  </tr>
					</thead>
					<tbody>
					  @php $currency = currency(); @endphp
					  @foreach($taxs as $tax)
					  <tr id="row_{{ $tax->id }}">
						<td class='tax_name'>{{ $tax->tax_name }}</td>
						<td class='rate'>{{ $currency." ".$tax->rate }}</td>	
						<td class='type'>{{ ucwords($tax->type) }}</td>	
						<td class="text-center">
						  <form action="{{action('TaxController@destroy', $tax['id'])}}" method="post">
							<a href="{{action('TaxController@edit', $tax['id'])}}" data-title="{{ _lang('Update Tax') }}" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
							<a href="{{action('TaxController@show', $tax['id'])}}" data-title="{{ _lang('View Tax') }}" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>
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


