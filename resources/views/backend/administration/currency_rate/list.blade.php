@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<span class="panel-title d-none">{{ _lang('Currency Exchange Rates') }}</span>
            @php $converter = get_option('currency_converter'); @endphp
			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
					  <tr>
						<th>{{ _lang('Currency') }}</th>
						<th>{{ _lang('Rate') }}</th>
						@if($converter == 'manual')
							<th class="text-center">{{ _lang('Edit') }}</th>
						@endif
					  </tr>
					</thead>
					<tbody>
					  
					  @foreach($currency_rates as $rate)
					  <tr id="row_{{ $rate->id }}">
						<td class='name'>{{ $rate->currency }}</td>
						<td class='subject'>{{ $rate->rate }}</td>
						@if($converter == 'manual')
							<td class="text-center">
								<a href="{{ action('UtilityController@currency_rates', $rate->id) }}" class="btn btn-warning btn-xs ajax-modal">{{ _lang('Edit') }}</a>
							</td>
						@endif
					  </tr>
					  @endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection


