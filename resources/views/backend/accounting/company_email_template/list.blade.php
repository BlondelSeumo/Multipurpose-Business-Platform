@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<a class="btn btn-primary btn-xs" data-title="{{ _lang('Add Email Template') }}" href="{{ route('company_email_template.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('List Email Template') }}</span>

			<div class="card-body">
			 <table class="table table-bordered data-table">
				<thead>
				  <tr>
					<th>{{ _lang('Name') }}</th>
					<th>{{ _lang('Subject') }}</th>
					<th class="text-center">{{ _lang('Action') }}</th>
				  </tr>
				</thead>
				<tbody>
				  
				  @foreach($companyemailtemplates as $companyemailtemplate)
				  <tr id="row_{{ $companyemailtemplate->id }}">
					<td class='name'>{{ $companyemailtemplate->name }}</td>
					<td class='subject'>{{ $companyemailtemplate->subject }}</td>
					<td class="text-center">
					  <form action="{{ action('CompanyEmailTemplateController@destroy', $companyemailtemplate['id']) }}" method="post">
						<a href="{{ action('CompanyEmailTemplateController@edit', $companyemailtemplate['id']) }}" class="btn btn-warning btn-xs"><i class="ti-pencil"></i></a>
						<a href="{{ action('CompanyEmailTemplateController@show', $companyemailtemplate['id']) }}" class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('View Email Template') }}"><i class="ti-eye"></i></a>
						{{ csrf_field() }}
						<input name="_method" type="hidden" value="DELETE">
						<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i></button>
					  </form>
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


