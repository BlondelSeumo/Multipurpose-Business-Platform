@extends('layouts.app')

@section('content')

@if (\Session::has('paypal_success'))
  <div class="alert alert-success text-center">
	<b>{{ \Session::get('paypal_success') }}</b>
  </div>
  <br>
@endif

@php 
$currency = currency(); 
$date_format = get_company_option('date_format','Y-m-d');
@endphp

<!--Start Card-->
<div class="row">
	<div class="col-md-3 mb-3">
		<div class="card">
			<div class="seo-fact sbg1">
				<div class="p-4">
					<div class="seofct-icon">
						<span>{{ _lang('Total Project') }}</span>
					</div>
					<h2>{{ $total_project }}</h2>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-md-3 mb-3">
		<div class="card">
			<div class="seo-fact sbg2">
				<div class="p-4">
					<div class="seofct-icon">
						<span>{{ _lang('Not Started Project') }}</span>
					</div>
					<h2>{{ isset($project_status['not_started']) ? $project_status['not_started'] : '0' }}</h2>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-3 mb-3">
		<div class="card">
			<div class="seo-fact sbg4">
				<div class="p-4">
					<div class="seofct-icon">
						<span>{{ _lang('Project In Progress') }}</span>
					</div>
					<h2>{{ isset($project_status['in_progress']) ? $project_status['in_progress'] : '0' }}</h2>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-md-3 mb-3">
		<div class="card">
			<div class="seo-fact sbg2">
				<div class="p-4">
					<div class="seofct-icon">
						<span>{{ _lang('Completed Project') }}</span>
					</div>
					<h2>{{ isset($project_status['completed']) ? $project_status['completed'] : '0' }}</h2>
				</div>
			</div>
		</div>
	</div>

</div><!--end row-->
<!--End Card-->


<div class="row d-flex align-items-stretch">

  <!-- Panel 1 -->
  <div class="col-md-6">
	 <div class="card h-100">
		<div class="card-body">
		    <h4 class="header-title mt-0">{{ _lang('My Recent Projects') }}</h4>
		    <div class="table-responsive card-scroll">
			    <table class="table">
				      <thead>
					    <tr>
							<th>{{ _lang('Name') }}</th>	
							<th>{{ _lang('Start Date') }}</th>
							<th>{{ _lang('End Date') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
						@foreach(Auth::user()->projects->take(10) as $project)
						    <tr>
								<td><a href="{{ action('ProjectController@show', $project->id) }}">{{ $project->name }}</a></td>
								<td>{{ date($date_format,strtotime($project->start_date)) }}</td>
								<td>{{ date($date_format,strtotime($project->end_date)) }}</td>
								<td>{!! clean(project_status($project->status)) !!}</td>
								<td>
									<form action="{{ action('ProjectController@destroy', $project['id']) }}" class="text-center" method="post">
										<a href="{{ action('ProjectController@edit', $project['id']) }}" data-title="'. _lang('Update Project') .'" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
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
  <!-- End Panel 1 -->

  <!-- Panel 2 -->
  <div class="col-md-6">
	 <div class="card h-100">
		<div class="card-body">
		   <h4 class="header-title mt-0">{{ _lang('My Recent Tasks') }}</h4>
		   <div class="table-responsive card-scroll">
			   <table class="table">
				    <thead>
					    <tr>
							<th>{{ _lang('Name') }}</th>	
							<th>{{ _lang('Priority') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th>{{ _lang('Deadline') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
						@foreach(Auth::user()->tasks->take(10) as $task)
						    <tr>
								<td><a href="{{ action('TaskController@show', $task->id) }}" data-title="{{ $task->title }}" class="ajax-modal">{{ $task->title }}</a></td>
								<td>{!! clean(task_priority($task->priority)) !!}</td>
								<td>
									<span class='badge badge-primary' style='background:{$task->status->color}'>{{ $task->status->title }}</span>
								</td>
								<td>{{ date($date_format,strtotime($task->end_date)) }}</td>
								<td>
									<form action="{{ action('TaskController@destroy', $task['id']) }}" class="text-center" method="post">
										<a href="{{ action('TaskController@edit', $task['id']) }}" data-title="'. _lang('Update Project') .'" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
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
  <!-- End Panel 2 -->
</div>

@endsection
