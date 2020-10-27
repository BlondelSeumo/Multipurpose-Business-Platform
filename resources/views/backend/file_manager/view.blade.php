@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
	<div class="card">
	<div class="d-none panel-title">{{ _lang('View File') }}</div>

	<div class="card-body">
	    <table class="table table-bordered">
			<tr><td>{{ _lang('Name') }}</td><td>{{ $filemanager->name }}</td></tr>
			<tr><td>{{ _lang('Mime Type') }}</td><td>{{ $filemanager->mime_type }}</td></tr>
			<tr><td>{{ _lang('File') }}</td><td>{{ $filemanager->file }}</td></tr>
			<tr><td>{{ _lang('Created By') }}</td><td>{{ $filemanager->created_by }}</td></tr>
	    </table>
	</div>
  </div>
 </div>
</div>
@endsection


