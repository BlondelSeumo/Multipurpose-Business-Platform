@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	
@php $time_format = get_company_option('time_format',24) == '24' ? 'H:i' : 'h:i A'; @endphp	

<table class="table table-bordered">
	<tr><td>{{ _lang('User') }}</td><td>{{ $timesheet->user->name }}</td></tr>
	<tr><td>{{ _lang('Task') }}</td><td>{{ $timesheet->task->title }}</td></tr>
	<tr><td>{{ _lang('Start Time') }}</td><td>{{ date("$date_format $time_format",strtotime($timesheet->start_time)) }}</td></tr>
	<tr><td>{{ _lang('End Time') }}</td><td>{{ date("$date_format $time_format",strtotime($timesheet->end_time)) }}</td></tr>
	<tr><td>{{ _lang('Total Hours') }}</td><td>{{ $timesheet->total_hour }}</td></tr>
	<tr><td>{{ _lang('Note') }}</td><td>{{ $timesheet->note }}</td></tr>
</table>

