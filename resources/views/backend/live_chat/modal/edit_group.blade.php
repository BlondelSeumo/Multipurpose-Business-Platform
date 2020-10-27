<form id="update-group" action="{{ url('live_chat/update_group/'.$group->id) }}" method="post" autocomplete="off">	
	{{ csrf_field() }}
	
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Group Name') }}</label>						
		<input type="text" class="form-control" name="name" value="{{ $group->name }}" required>
	  </div>
	</div>
	
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Group Members') }}</label>						
		<select class="form-control select2" id="group-members" name="members[]" multiple="true" required>
		 {{ create_option('users','id','name','',array('(company_id='=>company_id(),' OR id='=>company_id(), ') AND id !='=>Auth::user()->id)) }}
		 <!-- Client list-->
		 @foreach(\App\User::where('user_type','client')
		 ->join('contacts','contacts.user_id','users.id')
		 ->where('contacts.company_id',company_id())
		 ->select('users.*')->get() as $client)
            <option value="{{ $client->id }}">{{ $client->name }}</option>
		 @endforeach
		</select>
	  </div>
	</div>
	
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Description') }}</label>						
		<textarea class="form-control" name="description">{{ $group->description }}</textarea>
	  </div>
	</div>
				
	<div class="col-md-12">
	  <div class="form-group">
	    <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>
		<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
	  </div>
	</div>
</form>

<script>
(function($) {
    "use strict";
    
	$("#group-members").val([{{ object_to_string($group->group_members,'id') }}]);

})(jQuery);
</script>
