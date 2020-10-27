@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<span class="panel-title d-none">{{ _lang('Email Templates') }}</span>

			<div class="card-body">
			<table class="table table-bordered data-table">
			<thead>
			  <tr>
				<th>{{ _lang('Name') }}</th>
				<th>{{ _lang('Subject') }}</th>
				<th>{{ _lang('Action') }}</th>
			  </tr>
			</thead>
			<tbody>
			  
			  @foreach($emailtemplates as $emailtemplate)
			  <tr id="row_{{ $emailtemplate->id }}">
				<td class='name'>{{ ucwords(str_replace('_',' ',$emailtemplate->name)) }}</td>
				<td class='subject'>{{ $emailtemplate->subject }}</td>
				<td>
					<a href="{{action('EmailTemplateController@edit', $emailtemplate['id'])}}" class="btn btn-warning btn-xs">{{ _lang('Edit') }}</a>
					<a href="{{action('EmailTemplateController@show', $emailtemplate['id'])}}" class="btn btn-primary btn-xs">{{ _lang('View') }}</a>
				</td>

			  </tr>
			  @endforeach
			</tbody>
		  </table>
			</div>
		</div>
	</div>
</div>

@endsection


