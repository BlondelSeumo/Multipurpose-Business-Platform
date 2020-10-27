@extends('layouts.app')

@section('content')
<div class="row">
<div class="col-12">
<form method="post" class="validate" autocomplete="off" action="{{ route('services.import') }}" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-6">
			<div class="card">
				<div class="card-header bg-primary text-white">
				   <h5 class="panel-title">{{ _lang('Import Services') }}</h5>
				</div>
				<div class="card-body">
					{{ csrf_field() }}

					<div class="row">
						<div class="col-md-12">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Upload XLSX File') }}</label>						
							<input type="file" class="dropify" name="file" data-allowed-file-extensions="xlsx" required>
						  </div>
						</div>

						<div class="col-md-12">
						  <div class="form-group">
							<button type="submit" class="btn btn-primary btn-xs">{{ _lang('Import Products') }}</button>
						  </div>
						</div>
					</div>
				</div>
			</div>
	    </div>
		
	    <div class="col-md-6">
			<div class="card">
			    <div class="card-header bg-primary text-white">
				   <h5>{{ _lang('Instructions') }}</h5>
				</div>
			    <div class="card-body">
				   <ol class="pl-3">
				      <li>{{ _lang('Only XLSX file are allowed.') }}</li>
				      <li>{{ _lang('First row need to keep blank or use for column name only.') }}</li>
				      <li>{{ _lang('Required field must needed.') }}</li>
				      <li><a href="{{ asset('public/xlsx_sample/services.xlsx') }}">{{ _lang('Download Sample File') }}</a></li>
				   </ol>
				</div>
			</div>
		</div>	
    </div>
 </form>
</div>
</div>
@endsection


