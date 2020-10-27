<form method="post" class="ajax-submit" autocomplete="off" action="{{ action('FileManagerController@update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">				
	
	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Name') }}</label>						
		<input type="text" class="form-control" name="name" value="{{ $filemanager->name }}" required>
	 </div>
	</div>
	
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Folder') }}</label>						
		<select class="form-control select2" name="parent_id" value="{{ old('parent_id') }}">
			<option value="">{{ _lang('Root Directory') }}</option>
			@foreach($parent_directory as $dir)
				<option value="{{ $dir->id }}" {{ $dir->id == $filemanager->parent_id ? 'selected' : '' }}>{{ $dir->name }}</option>
			@endforeach
		</select>
	  </div>
	</div>

	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('File') }}</label>						
		<input type="file" class="form-control dropify" name="file" data-default-file="{{ asset('public/file_manager/'.$filemanager->file) }}">
	 </div>
	</div>
			
	<div class="form-group">
	  <div class="col-md-12">
		<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
	  </div>
	</div>
</form>

