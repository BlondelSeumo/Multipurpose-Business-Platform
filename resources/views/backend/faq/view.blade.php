@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
		    <div class="card-header">
				<h4 class="card-title">{{ _lang('View FAQ') }}</h4>
			</div>
			
			<div class="card-content">
				<div class="card-body">
	
				  <table class="table table-bordered">
					<tr><td>{{ _lang('Question') }}</td><td>{{ get_array_data($faq->question, get_option('language')) }}</td></tr>
					<tr><td>{{ _lang('Answer') }}</td><td>{!! clean(get_array_data($faq->answer,get_option('language'))) !!}</td></tr>
				  </table>
				</div>
			</div>
	    </div>
	</div>
</div>
@endsection


