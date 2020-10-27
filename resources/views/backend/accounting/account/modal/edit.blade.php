<form method="post" class="ajax-submit" autocomplete="off" action="{{action('AccountController@update', $id)}}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">				
	
	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Account Title') }}</label>						
		<input type="text" class="form-control" name="account_title" value="{{ $account->account_title }}" required>
	 </div>
	</div>

	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Opening Date') }}</label>						
		<input type="text" class="form-control datepicker" name="opening_date" value="{{ $account->opening_date }}" required>
	 </div>
	</div>

	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Account Number') }}</label>						
		<input type="text" class="form-control" name="account_number" value="{{ $account->account_number }}">
	 </div>
	</div>
	
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Account Currency') }}</label>						
		<select class="form-control select2 auto-select" data-selected="{{  $account->account_currency }}" name="account_currency" id="account_currency" disabled>
			<option value="">{{ _lang('Select One') }}</option>
			{{ get_currency_list( ) }}
		</select>
	  </div>
	</div>

	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Opening Balance') }}</label>						
		<input type="text" class="form-control float-field" name="opening_balance" value="{{ $account->opening_balance }}" disabled>
	 </div>
	</div>

	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Note') }}</label>						
		<textarea class="form-control" name="note">{{ $account->note }}</textarea>
	 </div>
	</div>
			
	<div class="form-group">
	  <div class="col-md-12">
		<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
	  </div>
	</div>
</form>


<script>
(function($) {
    "use strict";
    
	$("#account_currency").val("{{  $account->account_currency }}").trigger('change');

})(jQuery);
</script>
