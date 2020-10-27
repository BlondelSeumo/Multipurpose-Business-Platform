 <table class="table table-bordered">
	<tr><td>{{ _lang('Question') }}</td><td>{{ get_array_data($faq->question, get_option('language')) }}</td></tr>
	<tr><td>{{ _lang('Answer') }}</td><td>{!! clean(get_array_data($faq->answer,get_option('language'))) !!}</td></tr>
</table>

