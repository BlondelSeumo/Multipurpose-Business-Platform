<table class="table table-striped">	
	<tr>
		<td>
			<img class="notify-thumb-img float-left" src="{{ asset('public/uploads/profile/'.$notification->user->profile_picture) }}">
			<h4 class="pt-3 pl-2 float-left">{{ $notification->user->name }}</h4>
		</td>
	</tr>
	
	<tr>
	    <td>{{ $notification->data['title'] }}</td>
	</tr>
	
	<tr>
		<td>{{ $notification->data['content'] }}</td>
	</tr>
	
	<tr>
		<td>{{ $notification->created_at->diffForHumans() }}</td>
	</tr>
	
	<tr>
		<td><a href="{{ url($notification->data['url']) }}" class="btn btn-primary btn-xs">{{ _lang('Reference Link') }}</a></td>
    </tr>
</table>

