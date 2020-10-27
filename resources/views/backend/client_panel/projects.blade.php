@extends('layouts.app')

@section('content')

@php $date_format = get_company_option('date_format','Y-m-d'); @endphp

<div class="row">
	<div class="col-md-12">

		<div class="card">
			<span class="d-none panel-title">{{ _lang('Project List') }}</span>

			<div class="card-body">
				<table class="table table-bordered data-table">
				      <thead>
					    <tr>
							<th>{{ _lang('Name') }}</th>	
							<th>{{ _lang('Start Date') }}</th>
							<th>{{ _lang('End Date') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th>{{ _lang('Progress') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
						@foreach($projects as $project)
							<tr>
								<td><a href="{{ action('ClientController@view_project', $project->id) }}">{{ $project->name }}</a></td>
								<td>{{ date($date_format,strtotime($project->start_date)) }}</td>
								<td>{{ date($date_format,strtotime($project->end_date)) }}</td>
								<td>{!! clean(project_status($project->status)) !!}</td>
								<td>
									<div class="progress">
									  <div class="progress-bar" role="progressbar" style="width: {{ $project->progress }}%;" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">{{ $project->progress }}%</div>
									</div>
								</td>
								<td class="text-center">
									<a href="{{ action('ClientController@view_project', $project->id) }}" class="btn btn-primary btn-xs"><i class="ti-eye"></i></a>
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


