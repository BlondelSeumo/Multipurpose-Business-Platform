<form method="post" class="ajax-submit" autocomplete="off" action="{{ action('UtilityController@currency_rates', $currency_rate->id) }}">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">				
	
	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Currency') }}</label>						
		<input type="text" class="form-control" name="currency" value="{{ $currency_rate->currency }}" readonly="true">
	 </div>
	</div>
	
	<div class="col-md-12">
	 <div class="form-group">
		<label class="control-label">{{ _lang('Rate') }}</label>						
		<input type="text" class="form-control" name="rate" value="{{ $currency_rate->rate }}" required>
	 </div>
	</div>
				
	<div class="form-group">
	  <div class="col-md-12">
		<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
	  </div>
	</div>
</form>

