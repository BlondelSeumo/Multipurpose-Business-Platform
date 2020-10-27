@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header bg-primary text-white">
				<span class="panel-title">{{ _lang('View Role') }}</span>
			</div>
			
			<div class="card-body">
			    <table class="table table-bordered">
				    <tr><td>{{ _lang('Name') }}</td><td>{{ $role->name }}</td></tr>
					<tr><td>{{ _lang('Description') }}</td><td>{{ $role->description }}</td></tr>
			    </table>
			</div>
	    </div>
	</div>
</div>
@endsection


