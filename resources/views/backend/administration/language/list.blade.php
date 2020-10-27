@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
	    
		<a class="btn btn-primary btn-xs" href="{{ route('languages.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			
		<div class="card mt-2">
			
			<span class="panel-title d-none">{{ _lang('Languages') }}</span>
			<div class="card-body">
			 <div class="table-responsive">
				 <table class="table table-bordered">
					<thead>
					  <tr>
						<th>{{ _lang('Language Name') }}</th>
						<th>{{ _lang('Edit Translation') }}</th>
						<th>{{ _lang('Remove') }}</th>
					  </tr>
					</thead>
					<tbody>
					  
					  @foreach(get_language_list() as $language)
					  <tr>
						<td>{{ $language }}</td>
						<td>
							<a href="{{ action('LanguageController@edit', $language) }}" class="btn btn-secondary btn-xs">{{ _lang('Edit Translation') }}</a>
						</td>	
						<td>
							<form action="{{ action('LanguageController@destroy', $language) }}" method="post">
							   {{ csrf_field() }}
							   <input name="_method" type="hidden" value="DELETE">
							   <button class="btn btn-danger btn-xs btn-remove" type="submit">{{ _lang('Delete') }}</button>
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
</div>

@endsection


