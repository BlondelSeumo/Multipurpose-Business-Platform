@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="card">
			<span class="d-none panel-title">{{ _lang('Update Payment Method') }}</span>

			<div class="card-body">
			  <div class="col-md-6">
				<form method="post" class="validate" autocomplete="off" action="{{ action('PaymentMethodController@update', $id) }}" enctype="multipart/form-data">
					{{ csrf_field()}}
					<input name="_method" type="hidden" value="PATCH">				
					
					<div class="col-md-12">
					 <div class="form-group">
						<label class="control-label">{{ _lang('Name') }}</label>						
						<input type="text" class="form-control" name="name" value="{{ $paymentmethod->name }}" required>
					 </div>
					</div>

					<div class="col-md-12">
					  <div class="form-group">
						<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
					  </div>
					</div>
				</form>
			  </div>
			</div>
		</div>
	</div>
</div>

@endsection


