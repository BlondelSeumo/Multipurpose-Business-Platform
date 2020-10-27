@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('public/backend/assets/css/jquery-ui.min.css') }}">

<div class="row">
	<div class="col-lg-12">
		<a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Create New Lead') }}" href="{{ route('tasks.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
		<a class="btn btn-secondary btn-xs" href="{{ url('tasks') }}"><i class="ti-layout-column3"></i> {{ _lang('List View') }}</a>
		<div class="card mt-2">
		    
			<span class="panel-title d-none">{{ _lang('Task Kanban') }}</span>
			
			@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	
			
			<div class="card-body overflow-auto">
			    <div id="kanban-view">
					@foreach($task_status as $status)
					
					<ul class="kanban-col" data-status-id="{{ $status->id }}">
					    <li>
							<p class="kanban-title" style="background:{{ $status->color }}">{{ $status->title }}</p>
							<div class="cards">
								<ul class="status lead-status" data-task-status-id="{{ $status->id }}">
								
								    @foreach($status->tasks->take(20) as $task)
									<li data-task-id="{{ $task->id }}">
										<div class="card">
											<div class="card-body">
												@if(isset($task->assigned_user->name))
													<img src="{{ asset('public/uploads/profile/'.$task->assigned_user->profile_picture) }}" class="kanban-avatar" data-toggle="tooltip" data-placement="top" title="{{ $task->assigned_user->name }}">
												@else
													<img src="{{ asset('public/uploads/profile/default.png') }}" class="kanban-avatar" data-toggle="tooltip" data-placement="top" title="{{ _lang('No User Assigned') }}">
												@endif
												<a href="{{ action('TaskController@show', $task->id) }}" data-title="{{ $task->title }}"  class="lead-title ajax-modal">{{ $task->id.'#- '.$task->title }}</a>
											</div>	
										</div>
									</li>			
									@endforeach	

								</ul>	
								@if( isset($task) )	
									<button data-status-id="{{ $status->id }}" data-task-id="{{ $task->id }}" class="btn btn-info btn-block load-more">{{ _lang('Load More') }}</button>
							    @endif
							</div>
						</li>
					</ul>
					
					@endforeach
				
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('public/backend/assets/js/tasks.js') }}"></script>
@endsection