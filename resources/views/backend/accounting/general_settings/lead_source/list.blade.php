@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header bg-primary text-white">
				<span class="panel-title">{{ _lang('Lead Sources') }}</span>
				<a class="btn btn-primary btn-sm float-right ajax-modal" data-title="{{ _lang('Add Lead Source') }}" href="{{ route('lead_sources.create') }}">{{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table class="table table-bordered data-table">
					<thead>
					  <tr>
						<th>{{ _lang('Title') }}</th>
						<th>{{ _lang('Order') }}</th>
						<th>{{ _lang('Company Id') }}</th>
						<th class="text-center">{{ _lang('Action') }}</th>
					  </tr>
					</thead>
					<tbody> 
					    @foreach($leadsources as $leadsource)
					    <tr id="row_{{ $leadsource->id }}">
							<td class='title'>{{ $leadsource->title }}</td>
							<td class='order'>{{ $leadsource->order }}</td>
							<td class='company_id'>{{ $leadsource->company_id }}</td>
							
							<td class="text-center">
								<div class="dropdown">
								  <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ action('LeadSourceController@destroy', $leadsource['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">
									
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ action('LeadSourceController@edit', $leadsource['id']) }}" data-title="{{ _lang('Update Lead Source') }}" class="dropdown-item ajax-modal"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a>
										<a href="{{ action('LeadSourceController@show', $leadsource['id']) }}" data-title="{{ _lang('View Lead Source') }}" class="dropdown-item ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</a>
										<button class="btn-remove dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
									</div>
								  </form>
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