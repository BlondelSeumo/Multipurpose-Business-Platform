@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-md-12">
	<div class="panel panel-default">
	<div class="panel-heading">{{ _lang('View Contact Group') }}</div>

	<div class="panel-body">
	  <table class="table table-bordered">
		<tr><td>{{ _lang('Group') }}</td><td>{{ $contactgroup->group }}</td></tr>
		<tr><td>{{ _lang('Note') }}</td><td>{{ $contactgroup->note }}</td></tr>
	  </table>
	</div>
  </div>
 </div>
</div>
@endsection


