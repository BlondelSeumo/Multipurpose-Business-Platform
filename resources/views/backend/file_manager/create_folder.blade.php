@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
	<div class="card">
	<div class="d-none panel-title">{{ _lang('New Folder') }}</div>

	<div class="card-body">
	  <div class="row">
		  <div class="col-md-6">
			<form method="post" class="validate" autocomplete="off" action="{{ url('file_manager/store_folder') }}" enctype="multipart/form-data">
				{{ csrf_field() }}
				
				<div class="col-md-12">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Folder Name') }}</label>						
					<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
				  </div>
				</div>

				@php $parent_id = $parent_id != '' ? decrypt($parent_id) : ''; @endphp
				
				<div class="col-md-12">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Parent Folder') }}</label>						
					<select class="form-control select2" name="parent_id" value="{{ old('parent_id') }}">
						<option value="">{{ _lang('Root Directory') }}</option>
						@foreach($parent_directory as $dir)
							<option value="{{ $dir->id }}" {{ $parent_id==$dir->id ? 'selected' : '' }}>{{ $dir->name }}</option>
						@endforeach
					</select>
				  </div>
				</div>

					
				<div class="col-md-12">
				  <div class="form-group">
					<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
				  </div>
				</div>
			</form>
		  </div>
	  </div>
	</div>
  </div>
 </div>
</div>
@endsection


