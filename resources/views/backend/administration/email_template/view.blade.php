@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-md-12">
	<div class="card">
	<div class="d-none panel-title">{{ _lang('View Email Template') }}</div>

	<div class="card-body">
	  <table class="table table-striped">
		<tr><td>{{ $emailtemplate->subject }}</td></tr>
		<tr><td>{!! clean($emailtemplate->body) !!}</td></tr>		
	  </table>
	</div>
  </div>
 </div>
</div>
@endsection


