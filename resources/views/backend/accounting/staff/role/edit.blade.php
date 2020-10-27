@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header bg-primary text-white">
				<span class="panel-title">{{ _lang('Update Role') }}</span>
			</div>
			<div class="card-body">
				<form method="post" class="validate" autocomplete="off" action="{{ action('RoleController@update', $id) }}" enctype="multipart/form-data">
					{{ csrf_field()}}
					<input name="_method" type="hidden" value="PATCH">				
					<div class="row">
						<div class="col-md-6">
							<div class="col-md-12">
							    <div class="form-group">
								   <label class="control-label">{{ _lang('Name') }}</label>						
								   <input type="text" class="form-control" name="name" value="{{ $role->name }}" required>
							    </div>
							</div>

							<div class="col-md-12">
							    <div class="form-group">
								   <label class="control-label">{{ _lang('Description') }}</label>						
								   <textarea class="form-control" name="description">{{ $role->description }}</textarea>
							    </div>
							</div>

							
							<div class="col-md-12">
							    <div class="form-group">
								    <button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
							    </div>
							</div>
						</div>	
					</div>	
				</form>
			</div>
		</div>
	</div>
</div>

@endsection


