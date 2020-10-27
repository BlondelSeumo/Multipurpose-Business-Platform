@php $date_format = get_company_option('date_format','Y-m-d'); @endphp
<table class="table table-bordered">
	<tr><td>{{ _lang('Title') }}</td><td>{{ $task->title }}</td></tr>
	<tr><td>{{ _lang('Project') }}</td><td>{{ $task->project->name }}</td></tr>
	<tr><td>{{ _lang('Milestone') }}</td><td>{{ $task->milestone->title }}</td></tr>
	<tr><td>{{ _lang('Priority') }}</td><td>{{ ucwords($task->priority) }}</td></tr>
	<tr>
		<td>{{ _lang('Task Status') }}</td>
		<td><span class="badge badge-primary" style="background:{{ $task->status->color }}">{{ $task->status->title }}</span></td>
	</tr>
	<tr>
		<td>{{ _lang('Assigned User') }}</td>
		<td>{{ $task->assigned_user->name }}</td>
	</tr>
	<tr><td>{{ _lang('Start Date') }}</td><td>{{ date("$date_format",strtotime($task->start_date)) }}</td></tr>
	<tr><td>{{ _lang('End Date') }}</td><td>{{ date("$date_format",strtotime($task->end_date)) }}</td></tr>
	<tr>
		<td colspan="2">
			<h4>{{ _lang('Task Description') }}</h4><hr>
			<p>{{ $task->description }}</p>
		</td>
	</tr>
</table>

