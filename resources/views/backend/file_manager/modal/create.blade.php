<form method="post" class="ajax-submit" autocomplete="off" action="{{route('file_manager.store')}}" enctype="multipart/form-data">
	{{ csrf_field() }}
	
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Name') }}</label>						
		<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
	  </div>
	</div>

	@php $parent_id = $parent_id != '' ? decrypt($parent_id) : ''; @endphp
	
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Folder') }}</label>						
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
		<label class="control-label">{{ _lang('File') }}</label>						
		<input type="file" class="form-control dropify" name="file"  required>
	  </div>
	</div>
				
	<div class="col-md-12">
	  <div class="form-group">
	    <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
		<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
	  </div>
	</div>
</form>
