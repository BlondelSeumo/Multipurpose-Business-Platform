@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    
			<span class="panel-title d-none">{{ $project->name }}</span>
			
			@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	
			@php $time_format = get_company_option('time_format',24) == '24' ? 'H:i' : 'h:i A'; @endphp	
			@php $currency = currency() @endphp

			<div class="card-body">
				<ul class="nav nav-tabs">
				  <li class="nav-item">
				    <a class="nav-link active" data-toggle="tab" href="#project_details">{{ _lang('Project Details') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#task">{{ _lang('Task') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#time_sheet">{{ _lang('Time Sheet') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#milestones">{{ _lang('Milestones') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#invoices">{{ _lang('Invoices') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#expense">{{ _lang('Expense') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#files">{{ _lang('Files') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#notes">{{ _lang('Notes') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#activity_log">{{ _lang('Activity Log') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link ajax-modal" href="{{ action('ProjectController@edit', $project->id) }}" data-title="{{ _lang('Update Project') }}">{{ _lang('Edit') }}</a>
				  </li>
				</ul>

				<div class="tab-content mt-4">
					<div class="tab-pane active" id="project_details">
					    <div class="row">
					    	<div class="col-lg-6">
							    <table class="table table-bordered">
								    <tr><td>{{ _lang('Name') }}</td><td><b>{{ $project->name }}</b></td></tr>
									<tr><td>{{ _lang('Client') }}</td><td>{{ $project->client->contact_name }}</td></tr>
									<tr>
										<td>{{ _lang('Progress') }}</td>
										<td>
											<div class="progress">
											  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $project->progress }}%;" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100"><span>{{ $project->progress }}%</span></div>
											</div>
										</td>
									</tr>
									<tr><td>{{ _lang('Billing Type') }}</td><td>{{ ucwords($project->billing_type) }}</td></tr>
									<tr><td>{{ _lang('Status') }}</td><td>{!! clean(project_status($project->status)) !!}</td></tr>
									
									@if($project->billing_type == 'fixed')
										<tr><td>{{ _lang('Fixed Rate') }}</td><td>{{ decimalPlace($project->fixed_rate, $currency) }}</td></tr>
									@endif

									@if($project->billing_type == 'hourly')
										<tr><td>{{ _lang('Hourly Rate') }}</td><td>{{ decimalPlace($project->hourly_rate, $currency) }}</td></tr>
									@endif

									<tr><td>{{ _lang('Start Date') }}</td><td>{{ date("$date_format", strtotime($project->start_date)) }}</td></tr>
									<tr><td>{{ _lang('End Date') }}</td><td>{{ date("$date_format", strtotime($project->end_date)) }}</td></tr>
									<tr>
										<td colspan="2">
											<h4>{{ _lang('Project Description') }}</h4>
											<hr>
											{!! clean($project->description) !!}
										</td>
									</tr>			
							    </table>
							</div>

							<div class="col-lg-6">
								<div class="row">
                                   <div class="col-lg-6 mb-3">
                                   		<div class="card">
											<div class="seo-fact sbg1">
												<div class="p-4">
													<div class="seofct-icon">
													    <i class="ti-alarm-clock"></i> 
														<span class="float-right">{{ _lang('Total Hour Worked') }}</span>
													</div>
													<h2 class="text-right">
														{{ time_from_seconds($hour_completed->total_seconds) }} {{ _lang('Hour') }}
													</h2>
												</div>
											</div>
										</div>
									</div>

	                                @if($project->billing_type == 'hourly')
	                                    <div class="col-lg-6 mb-3">
	                                   		<div class="card">
												<div class="seo-fact sbg2">
													<div class="p-4">
														<div class="seofct-icon">
														    <i class="ti-bar-chart-alt"></i> 
															<span class="float-right">{{ _lang('Total Hour Cost') }}</span>
														</div>
														<h2 class="text-right">{{ decimalPlace(($hour_completed->total_seconds/3600) * $project->hourly_rate, $currency) }}</h2>
													</div>
												</div>
											</div>
	                                    </div>
	                                @else
										<div class="col-lg-6 mb-3">
	                                   		<div class="card">
												<div class="seo-fact sbg2">
													<div class="p-4">
														<div class="seofct-icon">
														    <i class="ti-user"></i> 
															<span class="float-right">{{ _lang('Fixed Cost') }}</span>
														</div>
														<h2 class="text-right">{{ decimalPlace($project->fixed_rate, $currency) }}</h2>
													</div>
												</div>
											</div>
	                                   </div>
	                                @endif

								</div><!--End First Row-->

								<div class="row">
									<div class="col-md-12">
										 <h5 class="text-center">{{ _lang('Project Members') }}</h5>
										 <hr>
										 <div class="table-responsive">
		                                     <table id="project_members_table" class="table">
												<thead>
												    <tr>
													    <th>{{ _lang('#') }}</th>
														<th>{{ _lang('Name') }}</th>
														<th>{{ _lang('Remove') }}</th>
												    </tr>
												</thead>
												<tbody>
												    @foreach($project->members as $project_member)
												    <tr data-id="row_{{ $project_member->id }}">
														<td>
															<img src="{{ asset('public/uploads/profile/'.$project_member->profile_picture) }}" class="project-avatar" data-toggle="tooltip" data-placement="top" title="{{ $project_member->name }}">
														</td>
														<td>{{ $project_member->name }}</td>
														<td><a href="{{ url('projects/delete_project_member/'.$project_member->id) }}" class="ajax-get-remove">{{ _lang('Remove') }}</a></td>
												    </tr>
												    @endforeach
					                       
												</tbody>
											</table>
										</div>
									</div>

								</div>

							</div>
						</div>
					</div>


					<!-- Task tab-->
                    <div class="tab-pane" id="task">
                    	<a class="btn btn-info btn-xs mb-4 ajax-modal" data-title="{{ _lang('Create Task') }}" href="{{ route('tasks.create') }}?project_id={{ $project->id }}">
							<i class="ti-plus"></i> {{ _lang('Add New') }}
						</a>
						<div class="table-responsive">
							<table id="tasks_table" class="table">
								<thead>
									<tr>
										<th>{{ _lang('Title') }}</th>
										<th>{{ _lang('Priority') }}</th>
										<th>{{ _lang('Task Status') }}</th>
										<th>{{ _lang('Assigned User') }}</th>
										<th>{{ _lang('Start Date') }}</th>
										<th>{{ _lang('End Date') }}</th>
										<th class="text-center">{{ _lang('Action') }}</th>
									</tr>
								</thead>
								<tbody>
									@foreach($tasks as $task)
										<tr>
											<td>{{ $task->title }}</td>
											<td>{!! clean(task_priority($task->priority)) !!}</td>
											<td>
												<span class='badge badge-primary' style='background:{{ $task->status->color }}'>{{ $task->status->title }}</span>
											</td>
											<td>{{ $task->assigned_user->name }}</td>
											<td>{{ $task->start_date }}</td>
											<td>{{ $task->end_date  }}</td>
											<td class="text-center">
												<form action="{{ action('TaskController@destroy', $task['id']) }}" class="text-center" method="post">
													<a href="{{ action('TaskController@show', $task['id']) }}" data-title="{{ $task->title }}" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>
													<a href="{{ action('TaskController@edit', $task['id']) }}" data-title="{{ _lang('Update Task') }}" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
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


                    <!--Time Sheet Tab-->
                    <div class="tab-pane" id="time_sheet">

						<a class="btn btn-info btn-xs mb-4 ajax-modal" data-title="{{ _lang('Log Time') }}" href="{{ route('timesheets.create') }}?project_id={{ $project->id }}">
							<i class="ti-plus"></i>  {{ _lang('Add New') }}
						</a>
						<div class="table-responsive">
							<table id="timesheets_table" class="table table-bordered">
								<thead>
									<tr>
										<th>{{ _lang('User') }}</th>
										<th>{{ _lang('Task') }}</th>
										<th>{{ _lang('Start Time') }}</th>
										<th>{{ _lang('End Time') }}</th>
										<th>{{ _lang('Total Hour') }}</th>
										<th class="text-center">{{ _lang('Action') }}</th>
									</tr>
								</thead>
								<tbody>
									@foreach($timesheets as $timesheet)
										<tr data-id="row_{{ $timesheet->id }}">
											<td class='user_id'>{{ $timesheet->user->name }}</td>
											<td class='task_id'>{{ $timesheet->task->title }}</td>
											<td class='start_time'>{{ date("$date_format $time_format",strtotime($timesheet->start_time)) }}</td>
											<td class='end_time'>{{ date("$date_format $time_format",strtotime($timesheet->end_time)) }}</td>
											<td class='total_hour'>{{ $timesheet->total_hour }}</td>
											
											<td class="text-center">
												<div class="dropdown">
												  <button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												  {{ _lang('Action') }}
												  <i class="fa fa-angle-down"></i></button>
												  </button>
												  <form action="{{ action('TimeSheetController@destroy', $timesheet['id']) }}" method="post">
													{{ csrf_field() }}
													<input name="_method" type="hidden" value="DELETE">
													
													<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
														<a href="{{ action('TimeSheetController@edit', $timesheet['id']) }}" data-title="{{ _lang('Update Log Time') }}" class="dropdown-item ajax-modal"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a>
														<a href="{{ action('TimeSheetController@show', $timesheet['id']) }}" data-title="{{ $timesheet->task->title }}" class="dropdown-item ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</a>
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

                    <!-- Project Milestone-->
                    <div class="tab-pane" id="milestones">
                    	<a class="btn btn-info btn-xs mb-4 ajax-modal" data-title="{{ _lang('Create Milestone') }}" href="{{ route('project_milestones.create') }}?project_id={{ $project->id }}"><i class="ti-plus"></i> {{ _lang('Create Milestone') }}</a>

						<div class="table-responsive">
							<table id="project_milestones_table" class="table">
								<thead>
									<tr>
										<th>{{ _lang('Title') }}</th>
										<th>{{ _lang('Due Date') }}</th>
										<th>{{ _lang('Status') }}</th>
										<th class="text-right">{{ _lang('Cost') }}</th>
										<th class="text-center">{{ _lang('Action') }}</th>
									</tr>
								</thead>
								<tbody>
									@foreach($project_milestones as $projectmilestone)
									<tr data-id="row_{{ $projectmilestone->id }}">
										<td class='title'>{{ $projectmilestone->title }}</td>
										<td class='due_date'>{{ date("$date_format",strtotime($projectmilestone->due_date)) }}</td>
										<td class='status'>{!! clean(project_status($projectmilestone->status)) !!}</td>
										<td class='cost text-right'>{{ decimalPlace($projectmilestone->cost,$currency) }}</td>
										
										<td class="text-center">
											<div class="dropdown">
											  <button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											  {{ _lang('Action') }}
											  <i class="fa fa-angle-down"></i></button>
											  </button>
											  <form class="ajax-remove" action="{{ action('ProjectMilestoneController@destroy', $projectmilestone['id']) }}" method="post">
												{{ csrf_field() }}
												<input name="_method" type="hidden" value="DELETE">
												
												<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
													<a href="{{ action('ProjectMilestoneController@edit', $projectmilestone['id']) }}" data-title="{{ _lang('Update Milestone') }}" class="dropdown-item ajax-modal"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a>
													<a href="{{ action('ProjectMilestoneController@show', $projectmilestone['id']) }}" data-title="{{ _lang('View Milestone') }}" class="dropdown-item ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</a>
													<button class="dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
												</div>
											  </form>
											</div>
										</td>
									</tr>
									@endforeach
							
									<tr data-id="milestone_id">
										<td class="title"></td>
										<td class='due_date'></td>
										<td class="status"></td>
										<td class="cost text-right"></td>
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

                    <!--Start invoice tab-->
                    <div class="tab-pane" id="invoices">

                    	<a href="{{ route('invoices.create') }}?related_to=projects&project_id={{ $project->id }}" class="btn btn-info btn-xs mb-4"><i class="ti-plus"></i> {{ _lang('Create New') }}</a>

						<div class="table-responsive">
							<table id="invoice-table" class="table table-bordered">
								<thead>
								  <tr>
									<th>{{ _lang('Invoice Number') }}</th>
									<th>{{ _lang('Due Date') }}</th>
									<th class="text-right">{{ _lang('Grand Total') }}</th>
									<th class="text-right">{{ _lang('Paid') }}</th>
									<th class="text-center">{{ _lang('Status') }}</th>
									<th class="text-center">{{ _lang('Action') }}</th>
								  </tr>
								</thead>
								<tbody>
									@foreach($invoices as $invoice)
										<tr>
											<td class='invoice_number'>{{ $invoice->invoice_number }}</td>
											<td class='due_date'>{{ date($date_format,strtotime($invoice->due_date)) }}</td>
											<td class='grand_total text-right'>{{ decimalPlace($invoice->grand_total, $currency) }}</td>
											<td class='paid text-right'>{{ decimalPlace($invoice->paid, $currency) }}</td>
											<td class='status text-center'>{!! strip_tags(invoice_status($invoice->status),'<span>') !!}</td>
											<td class="text-center">

												<div class="dropdown">
													<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}
													<i class="fa fa-angle-down"></i></button>
													<ul class="dropdown-menu">
														<a class="dropdown-item" href="{{ action('InvoiceController@edit', $invoice->id) }}"><i class="fas fa-edit"></i> {{ _lang('Edit') }}</a>
														<a class="dropdown-item" href="{{ action('InvoiceController@show', $invoice->id) }}" data-title="{{ _lang('View Invoice') }}" data-fullscreen="true"><i class="fas fa-eye"></i> {{ _lang('View') }}</a>
														<a class="dropdown-item ajax-modal" href="{{ url('invoices/create_payment/'.$invoice->id) }}" data-title="{{ _lang('Make Payment') }}"><i class="fas fa-credit-card"></i> {{ _lang('Make Payment') }}</a>
														<a class="dropdown-item ajax-modal" href="{{ url('invoices/view_payment/'.$invoice->id) }}" data-title="{{ _lang('View Payment') }}" data-fullscreen="true"><i class="fas fa-credit-card"></i> {{ _lang('View Payment') }}</a>
														
														<form action="{{action('InvoiceController@destroy', $invoice['id'])}}" method="post">									
															{{ csrf_field() }}
															<input name="_method" type="hidden" value="DELETE">
															<button class="button-link btn-remove" type="submit"><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>
														</form>
															
													</ul>
												</div>
											</td>
										 </tr>
									@endforeach
								</tbody>
							</table>
						</div>
                    </div>
                    <!--End Invoice Tab-->


                    <!--Start Expense tab-->
                    <div class="tab-pane" id="expense">

                    	<a href="{{ route('expense.create') }}?related_to=projects&project_id={{ $project->id }}" data-title="{{ _lang('Add Expense') }}" class="btn btn-info btn-xs ajax-modal mb-4"><i class="ti-plus"></i> {{ _lang('Create New') }}</a>

						<div class="table-responsive">
							<table id="expense-table" class="table table-bordered">
								<thead>
									<tr>
										<th>{{ _lang('Date') }}</th>
										<th>{{ _lang('Account') }}</th>
										<th>{{ _lang('Expense Type') }}</th>
										<th class="text-right">{{ _lang('Amount') }}</th>
										<th>{{ _lang('Method') }}</th>
										<th class="action-col">{{ _lang('Action') }}</th>
									</tr>
								</thead>
								<tbody>
									@foreach($expenses as $expense)
										<tr>
											<td class='trans_date'>{{ date("$date_format",strtotime($expense->trans_date)) }}</td>
											<td class='account_id'>{{ $expense->account->account_title }}</td>
											<td class='chart_id'>{{ $expense->expense_type->name }}</td>
											<td class='amount text-righ'>{{ decimalPlace($expense->amount, $currency) }}</td>
											<td class='payment_method_id'>{{ $expense->payment_method->name }}</td>
											<td class="text-center">

												<div class="dropdown">
													<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}
													<i class="fa fa-angle-down"></i></button>
													<ul class="dropdown-menu">
														<a class="dropdown-item ajax-modal" data-title="{{ _lang('Update Expense') }}" href="{{ action('ExpenseController@edit', $expense->id) }}"><i class="fas fa-edit"></i> {{ _lang('Edit') }}</a>
														
														<a class="dropdown-item ajax-modal" href="{{ action('ExpenseController@show', $expense->id) }}" data-title="{{ _lang('View Expense') }}"><i class="fas fa-eye"></i> {{ _lang('View') }}</a>
													
														<form action="{{action('ExpenseController@destroy', $expense['id'])}}" method="post">									
															{{ csrf_field() }}
															<input name="_method" type="hidden" value="DELETE">
															<button class="button-link btn-remove" type="submit"><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>
														</form>
															
													</ul>
												</div>
											</td>
										 </tr>
									@endforeach
								</tbody>
							</table>
						</div>
                    </div>
                    <!--End Invoice Tab-->


                    <div class="tab-pane" id="files">
						<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('projects.upload_file') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="row">
								<input type="hidden" name="related_id" value="{{ $project->id }}" required>

								<div class="col-md-12">
								    <div class="form-group">
									    <label class="control-label">{{ _lang('Upload File') }}</label>
									    <input type="file" class="form-control dropify" name="file" required>
								    </div>
								</div>
								
								<div class="col-md-12">
								    <div class="form-group">
									    <button type="submit" class="btn btn-primary">{{ _lang('UPLOAD NOW') }}</button>
								    </div>
								</div>
							</div>			
					    </form>
					    <table id="files_table" class="table table-bordered">
							<thead>
							    <tr>
								    <th>{{ _lang('Upload Date') }}</th>
								    <th>{{ _lang('Uploaded') }}</th>
									<th>{{ _lang('File') }}</th>
									<th class="text-center">{{ _lang('Remove') }}</th>
							    </tr>
							</thead>
							<tbody>
							    @foreach($projectfiles as $projectfile)
							    <tr data-id="row_{{ $projectfile->id }}">
							    	<td class="created_at">{{ date("$date_format $time_format", strtotime($projectfile->created_at)) }}</td>
									<td class='user_id'><a href="{{action('StaffController@show', $projectfile->user->id)}}" data-title="{{ _lang('View Staf Information') }}"class="ajax-modal-2">{{ $projectfile->user->name }}</a></td>
									<td class='file'><a href="{{ url('projects/download_file/'.$projectfile->file) }}">{{ $projectfile->file }}</a></td>
									<td class="remove text-center"><a class="ajax-get-remove" href="{{ url('projects/delete_file/'.$projectfile->id) }}">{{ _lang('Remove') }}</a></td>
							    </tr>
							    @endforeach
							    <tr data-id="files_id">
							    	<td class="created_at"></td>
							    	<td class="user_id"></td>
									<td class='file'></td>
									<td class="remove text-center"></td>
								</tr>
							</tbody>
						</table>
					</div><!-- End File Tab-->


					<div class="tab-pane" id="notes">
						<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('projects.create_note') }}">
							{{ csrf_field() }}
							<div class="row">
								<input type="hidden" name="related_id" value="{{ $project->id }}" required>
							
								<div class="col-md-12">
								    <div class="form-group">
									    <label class="control-label">{{ _lang('Note') }}</label>						
									    <textarea class="form-control" name="note" required>{{ old('note') }}</textarea>
								    </div>
								</div>
								
								<div class="col-md-12">
								    <div class="form-group">
									    <button type="submit" class="btn btn-primary">{{ _lang('ADD NOTE') }}</button>
								    </div>
								</div>
							</div>			
					    </form>

					    <div class="crm-scroll">
						    <table id="notes_table" class="table">
								<tbody>
								    @foreach($notes as $note)
								    <tr data-id="row_{{ $note->id }}">
										<td class='created'>
											<small>
												{{ $note->user->name }}
												({{ date("$date_format $time_format", strtotime($note->created_at)) }})<br>
												{{ $note->note }}
											</small>
										</td>	
										<td class="action wp-100">
											<a href="{{ url('projects/delete_note/'.$note->id) }}" class="note-remove ajax-get-remove"><i class="far fa-trash-alt text-danger"></i></a>
										</td>
								    </tr>
								    @endforeach

								    <tr data-id="notes_id">
										<td class='created'></td>	
										<td class="action wp-100"></td>
								    </tr>
								</tbody>
							</table>
						</div>
					</div>	<!-- End Note Tab-->

					<div class="tab-pane" id="activity_log">

					     <div class="crm-scroll">
						    <table id="activity_log_table" class="table table-bordered">
								<tbody>
								</tbody>
							</table>
						</div>
					</div> <!-- End activity_log Tab-->

				</div>
			</div>
	    </div>
	</div>
</div>
@endsection


@section('js-script')
<script>

$('.nav-tabs a').on('shown.bs.tab', function(event){
	var tab = $(event.target).attr("href");
	if(tab == '#activity_log'){
        $.ajax({
        	url: "{{ url('projects/get_logs_data/'.$project->id) }}",
        	beforeSend: function(){
        		$("#preloader").fadeIn();
        	},success: function(data){
        		$("#preloader").fadeOut();
        		var json = JSON.parse(data);
				var rows = '';

        		$.each(json, function(index, element) {
				    rows += `<tr id="row_${element.id}">
									<td class='created'>
										<small>
											${element.created_at}<br>
											${element.created_by.name} - ${element.activity}
										</small>
									</td>	
							    </tr>`;
							    
				});

				$("#activity_log_table tbody").html(rows);

        	}
        });
	}


	var url = "{{ url('projects/'.$project->id) }}";
	//location.href = url + "?tab=" + tab;
    history.pushState({}, null, url + "?tab=" + tab.substring(1));
});

@if(isset($_GET['tab']))
   $('.nav-tabs a[href="#{{ $_GET['tab'] }}"]').tab('show')
@endif

</script>
@endsection	
