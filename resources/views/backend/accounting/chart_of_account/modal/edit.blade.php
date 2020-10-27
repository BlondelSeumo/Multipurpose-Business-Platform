<form method="post" class="ajax-submit" autocomplete="off" action="{{action('ChartOfAccountController@update', $id)}}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">				
	
	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Name') }}</label>						
		<input type="text" class="form-control" name="name" value="{{ $chartofaccount->name }}" required>
	 </div>
	</div>

	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Type') }}</label>						
		<select class="form-control auto-select" data-selected="{{ $chartofaccount->type }}" name="type" id="type" required>
			<option value="income">{{ _lang('Income') }}</option>
			<option value="expense">{{ _lang('Expense') }}</option>
		</select>
	  </div>
	</div>
		
	<div class="form-group">
	  <div class="col-md-12">
		<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
	  </div>
	</div>
</form>
