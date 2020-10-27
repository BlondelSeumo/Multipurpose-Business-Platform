<table class="table table-bordered">
	<tr><td>{{ _lang('Related To') }}</td><td>{{ ucwords($companyemailtemplate->related_to) }}</td></tr>
	<tr><td>{{ _lang('Name') }}</td><td>{{ $companyemailtemplate->name }}</td></tr>
	<tr><td>{{ _lang('Subject') }}</td><td>{{ $companyemailtemplate->subject }}</td></tr>
	<tr><td>{{ _lang('Body') }}</td><td>{!! clean($companyemailtemplate->body) !!}</td></tr>
</table>



