@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
	<div class="card">
	<span class="d-none panel-heading">{{ _lang('View Tax') }}</span>

	<div class="card-body">
	    <table class="table table-bordered">
			<tr><td>{{ _lang('Tax Name') }}</td><td>{{ $tax->tax_name }}</td></tr>
			<tr><td>{{ _lang('Rate') }}</td><td>{{ $tax->rate }}</td></tr>
			<tr><td>{{ _lang('Type') }}</td><td>{{ ucwords($tax->type) }}</td></tr>
	    </table>
	</div>
  </div>
 </div>
</div>
@endsection


