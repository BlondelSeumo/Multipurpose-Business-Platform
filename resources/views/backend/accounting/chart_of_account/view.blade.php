@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-md-12">
	<div class="panel panel-default">
	<div class="panel-heading">{{ _lang('View Income/Expense Type') }}</div>

	<div class="panel-body">
	  <table class="table table-bordered">
		<tr><td>{{ _lang('Name') }}</td><td>{{ $chartofaccount->name }}</td></tr>
		<tr><td>{{ _lang('Type') }}</td><td>{{ ucwords($chartofaccount->type) }}</td></tr>
	  </table>
	</div>
  </div>
 </div>
</div>
@endsection


