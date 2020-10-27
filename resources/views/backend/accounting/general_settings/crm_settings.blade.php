@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-sm-3">
		<ul class="nav flex-column nav-tabs settings-tab" role="tablist">
		   <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#lead_statuses">{{ _lang('Lead Status') }}</a></li>
		   <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#lead_source">{{ _lang('Lead Sources') }}</a></li>
		   <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#task_status">{{ _lang('Task Status') }}</a></li>
		</ul>
	</div>
	  
	<div class="col-sm-9">
		<div class="tab-content">
			<div id="lead_statuses" class="tab-pane active">
				<div class="card">
				  <span class="d-none panel-title">{{ _lang('CRM Settings') }}</span>
				  <div class="card-body">
					 <h5 class="card-title"><span>{{ _lang('Lead Statuses') }}</span>
						<button class="btn btn-primary btn-xs float-right modal-add ajax-modal" data-title="{{ _lang('Add New Lead Status') }}" data-href="{{ route('lead_statuses.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</button>
					 </h5>
					 <table id="lead_status_table" class="table table-bordered data-table">
						<thead>
						  <tr>
						  	<th>{{ _lang('Order') }}</th>
							<th>{{ _lang('Title') }}</th>
							<th class="text-center">{{ _lang('Color') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						  </tr>
						</thead>
						<tbody>
						  
						  @foreach($leadstatuss as $leadstatus)
						  <tr data-id="row_{{ $leadstatus->id }}">
						  	<td class='order'>{{ $leadstatus->order }}</td>
							<td class='title'>{{ $leadstatus->title }}</td>
							<td class='color text-center'><div class="rounded-circle color-circle" style="background:{{ $leadstatus->color }}"></div></td>
							
							<td class="text-center">
								<div class="dropdown">
								  <button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="lead_status_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  <i class="fa fa-angle-down"></i>
								  </button>
								  <form action="{{ action('LeadStatusController@destroy', $leadstatus['id']) }}" class="ajax-remove" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">
									
									<div class="dropdown-menu" aria-labelledby="lead_status_dropdown">
										<button data-href="{{ action('LeadStatusController@edit', $leadstatus['id']) }}" data-title="{{ _lang('Update Lead Status') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</button>
										<button class="dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
									</div>
								  </form>
								</div>
							</td>
						  </tr>
						  @endforeach
						    <tr data-id="milestone_id">
								<td class="order"></td>
								<td class="title"></td>
								<td class='color text-center'></td>
								<td class="text-center">
									<div class="dropdown">
									  <button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									  {{ _lang('Action') }}
									  <i class="fa fa-angle-down"></i></button>
									  </button>
									  <form class="ajax-remove" action="" method="post">
										{{ csrf_field() }}
										<input name="_method" type="hidden" value="DELETE">
										
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<a href="" data-title="" class="dropdown-item ajax-modal"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a>
											<a href="" data-title="" class="dropdown-item ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</a>
											<button class="dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
										</div>
									  </form>
									</div>
								</td>
							</tr>
						</tbody>
					  </table>
					  
				  </div>
			  </div>
		  </div>
		 
		  <div id="lead_source" class="tab-pane">
			  <div class="card">
				<div class="card-body">
				    <h5 class="card-title"><span>{{ _lang('Lead Sources') }}</span>
						<button class="btn btn-primary btn-xs float-right modal-add ajax-modal" data-title="{{ _lang('Add Lead Source') }}" data-href="{{ route('lead_sources.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</button>
					</h5>
					<table id="lead_source_table" class="table table-bordered data-table">
						<thead>
						  <tr>
							<th>{{ _lang('Title') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						  </tr>
						</thead>
						<tbody> 
							@foreach($leadsources as $leadsource)
							<tr data-id="row_{{ $leadsource->id }}">
								<td class='title'>{{ $leadsource->title }}</td>
	
								<td class="text-center">
									<div class="dropdown">
									  <button class="btn btn-primary btn-xs dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									  {{ _lang('Action') }}
									  <i class="fa fa-angle-down"></i>
									  </button>
									  <form action="{{ action('LeadSourceController@destroy', $leadsource['id']) }}" class="ajax-remove" method="post">
										{{ csrf_field() }}
										<input name="_method" type="hidden" value="DELETE">
										
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<a href="{{ action('LeadSourceController@edit', $leadsource['id']) }}" data-title="{{ _lang('Update Lead Source') }}" class="dropdown-item ajax-modal"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a>
											<a href="{{ action('LeadSourceController@show', $leadsource['id']) }}" data-title="{{ _lang('View Lead Source') }}" class="dropdown-item ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</a>
											<button class="dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
										</div>
									  </form>
									</div>
								</td>
							</tr>
							@endforeach
							<tr data-id="milestone_id">
								<td class="title"></td>
								<td class="text-center">
									<div class="dropdown">
									  <button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									  {{ _lang('Action') }}
									  <i class="fa fa-angle-down"></i></button>
									  </button>
									  <form class="ajax-remove" action="" method="post">
										{{ csrf_field() }}
										<input name="_method" type="hidden" value="DELETE">
										
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<a href="" data-title="" class="dropdown-item ajax-modal"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a>
											<a href="" data-title="" class="dropdown-item ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</a>
											<button class="dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
										</div>
									  </form>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			  </div>
			</div><!--End Lead Source Tab-->
			
			<div id="task_status" class="tab-pane">
				<div class="card">
				  <div class="card-body">
					 <h5 class="card-title"><span>{{ _lang('Task Status') }}</span>
						<button class="btn btn-primary btn-xs float-right modal-add ajax-modal" data-title="{{ _lang('New Task Status') }}" data-href="{{ route('task_statuses.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</button>
					 </h5>
					 <table id="task_status_table" class="table table-bordered data-table">
						<thead>
						  <tr>
						  	<th>{{ _lang('Order') }}</th>
							<th>{{ _lang('Title') }}</th>
							<th class="text-center">{{ _lang('Color') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						  </tr>
						</thead>
						<tbody>
						  
						  @foreach($task_statuss as $taskstatus)
						  <tr data-id="row_{{ $taskstatus->id }}">
						  	<td class='order'>{{ $taskstatus->order }}</td>
							<td class='title'>{{ $taskstatus->title }}</td>
							<td class='color text-center'><div class="rounded-circle color-circle" style="background:{{ $taskstatus->color }}"></div></td>
							
							<td class="text-center">
								<div class="dropdown">
								  <button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="lead_status_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  <i class="fa fa-angle-down"></i>
								  </button>
								  <form action="{{ action('TaskStatusController@destroy', $taskstatus['id']) }}" class="ajax-remove" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">
									
									<div class="dropdown-menu" aria-labelledby="lead_status_dropdown">
										<button data-href="{{ action('TaskStatusController@edit', $taskstatus['id']) }}" data-title="{{ _lang('Update Lead Status') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</button>
										<button class="dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
									</div>
								  </form>
								</div>
							</td>
						  </tr>
						  @endforeach
							<tr data-id="milestone_id">
								<td class="order"></td>
								<td class="title"></td>
								<td class='color text-center'></td>
								<td class="text-center">
									<div class="dropdown">
									  <button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									  {{ _lang('Action') }}
									  <i class="fa fa-angle-down"></i></button>
									  </button>
									  <form class="ajax-remove" action="" method="post">
										{{ csrf_field() }}
										<input name="_method" type="hidden" value="DELETE">
										
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<a href="" data-title="" class="dropdown-item ajax-modal"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a>
											<a href="" data-title="" class="dropdown-item ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</a>
											<button class="dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
										</div>
									  </form>
									</div>
								</td>
							</tr>
						</tbody>
					  </table>
					  
				  </div>
			  </div>
		  	</div>
				
		</div>
	</div>
</div>
@endsection

