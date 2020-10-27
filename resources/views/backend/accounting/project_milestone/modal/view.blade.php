<table class="table table-bordered">
	<tr><td>{{ _lang('Title') }}</td><td>{{ $projectmilestone->title }}</td></tr>
	<tr><td>{{ _lang('Description') }}</td><td>{{ $projectmilestone->description }}</td></tr>
	<tr><td>{{ _lang('Due Date') }}</td><td>{{ $projectmilestone->due_date }}</td></tr>
	<tr><td>{{ _lang('Status') }}</td><td>{!! clean(project_status($projectmilestone->status)) !!}</td></tr>
	<tr><td>{{ _lang('Cost') }}</td><td>{{ currency().' '.$projectmilestone->cost }}</td></tr>
</table>

