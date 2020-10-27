<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
			  <form method="post" class="validate" autocomplete="off" action="{{ url('client/select_business') }}">
				{{ csrf_field() }}

				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-info">
							<span>
								{{ _lang('It Works for chat widget only. If you are connected with multiple business then you may choose business for showing chat widget. Because some business not allow chat widget !') }}
							</span>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
						   <label class="control-label">{{ _lang('Business Name') }}</label>						
						   <select class="form-control select2" name="company_id" required>
							<option value="">{{ _lang('Select One') }}</option>
							@foreach(Auth::user()->client as $client)
							   <option value="{{ $client->company->id }}" {{ session('company_id') == $client->company->id ? 'selected' : '' }}>{{ $client->company->business_name }}</option>
							@endforeach
						   </select>
						</div>
					</div>
					
					<div class="col-md-12">
						<div class="form-group">
							<button type="submit" class="btn btn-primary btn-block">{{ _lang('Save') }}</button>
						</div>
					</div>
				</div>
			  </form>
			</div>
	    </div>
    </div>
</div>



