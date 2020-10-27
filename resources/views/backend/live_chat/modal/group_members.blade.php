<table class="table table-bordered">
  <tbody>
       <tr>
		  <td><h4>{{ _lang('Total').' '.$group->group_members->count().' '._lang('Member') }}</h4></td>
	   </tr>
      @foreach($group->group_members as $member)
	    @if($member->id == $group->created_by)
		    <tr>
			  <td><b>{{ $member->name.' - '._lang('Group Admin') }}</b></td>
			</tr>
		@else	  
			<tr>
				<td><b>{{ $member->name }}</b></td>
			</tr>	
        @endif		
	  @endforeach
	  
	  @if($group->description != "")
		 <tr>
			<td>{{ $group->description }}</td>
		 </tr>	
	  @endif
	  
  <tbody>
</table>