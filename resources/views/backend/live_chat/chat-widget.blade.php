
@php $chat_users = chat_user_list(); @endphp

<div class="chat-online-users">
	<div class="col-md-12 online-users-header rounded-top bg-primary text-white">
		<div class="row">
			<div class="col-6 username pl-2">
				<h6 class="m-0 text-white">{{ _lang('Online') }} (<span class="online-user-count">0</span>)</h6>
			    <div class="widget-notification widget-notification-main {{ unread_message_count() > 0 ? 'show' : 'hidden' }}">{{ unread_message_count() }}</div>
			</div>
			<div class="col-6 options text-right pr-2">
				<i class="fa fa-times hide-online-user"></i>
			</div>
		</div>
	</div>
	<div class="chat-user-list">
	    <label>{{ _lang('Employee') }}</label>
		<ul class="p-0 m-0" id="widget-staff-list"> 
			@foreach($chat_users['staffs'] as $widget_staff)
				<li id="user-{{ $widget_staff->id }}">
					<a href="#" data-group="0" data-id="{{ $widget_staff->id }}" data-name="{{ $widget_staff->name }}">
					    <span class="contact-status offline"></span>
						<img src="{{ $widget_staff->profile_picture != "" ? asset('public/uploads/profile/'.$widget_staff->profile_picture) :  asset('public/images/avatar.png') }}">
						<span>{{ $widget_staff->name }}</span>
						<div class="widget-notification float-right mt-1 {{ $widget_staff->unread_message_count > 0 ? 'show' : 'hidden' }}">{{ $widget_staff->unread_message_count }}</div>
					</a>
				</li>
			@endforeach
		</ul>
		
		@if(Auth::user()->user_type != 'client')
		    @if(count($chat_users['clients']) > 0)
	           <label>{{ _lang('Clients') }}</label>
		    @endif
			<ul class="p-0 m-0" id="widget-client-list"> 
				@foreach($chat_users['clients'] as $widget_client)
					<li id="user-{{ $widget_client->id }}">
						<a href="#" data-group="0" data-id="{{ $widget_client->id }}" data-name="{{ $widget_client->name }}">
						    <span class="contact-status offline"></span>
							<img src="{{ $widget_client->profile_picture != "" ? asset('public/uploads/profile/'.$widget_client->profile_picture) :  asset('public/images/avatar.png') }}">
							<span>{{ $widget_client->name }}</span>
							<div class="widget-notification float-right mt-1 {{ $widget_client->unread_message_count > 0 ? 'show' : 'hidden' }}">{{ $widget_client->unread_message_count }}</div>
						</a>
					</li>
				@endforeach
			</ul>
		@endif
		
		
		@if(count($chat_users['chat_groups']) > 0)
           <label>{{ _lang('Groups') }}</label>
	    @endif
		<ul class="p-0 m-0" id="widget-group-list"> 
			@foreach($chat_users['chat_groups'] as $widget_group)
			@php $msg_count = group_message_count($widget_group->id) @endphp
			<li id="group-{{ $widget_group->id }}">
				<a href="#" data-group="1" data-id="{{ $widget_group->id }}" data-name="{{ $widget_group->name }}">
					<div class="group-img">{{ get_initials($widget_group->name) }}</div>
					<span class="group-name">{{ $widget_group->name }}</span>
				    <div class="widget-notification float-right mt-1 {{ $msg_count > 0 ? 'show' : 'hidden' }}">{{ $msg_count }}</div>
				</a>
			</li>
			@endforeach
		</ul>
	</div>
</div>

<div class="chat-main">
	<div class="col-md-12 chat-header rounded-top bg-primary text-white">
		<div class="row">
			<div class="col-6 username pl-2">
				<i class="fa fa-circle text-green" aria-hidden="true"></i>
				<h6 class="m-0 receiver"></h6>
				<div class="widget-notification onchat-notification hidden"></div>
			</div>
			<div class="col-6 options text-right pr-2">
				<i class="fas fa-volume-up" id="mute-sound"></i>

				<i class="fas fa-times close-chat-box"></i>
			</div>
		</div>
	</div>
	<div class="chat-content">
	    <div id="chat-preloader">    
			<i class="fas fa-circle-notch fa-pulse"></i>
		</div>
		<div class="col-md-12 chats border" id="widget-chat-content">
			<ul class="p-0 mt-1">	   
				<!--<li class="send-msg">
					<p>Hi</p>
				</li>
				
				<li class="receive-msg">      
					<img src="http://nicesnippets.com/demo/image1.jpg">
					<p>Hello</p>
				</li>-->
			</ul>
		</div>
		<div class="col-md-12 message-box border pl-2 pr-2 border-top-0">
			<form id="widget-chat-form" autocomplete="off">
				<input type="text" name="message" id="widget-message" class="pl-0 pr-0 w-100" placeholder="Type a message..." />
				<input type="file" name="file" id="file" class="d-none" onchange="readFile(this);">
				<div class="tools text-right">
					<i class="fa fa-paperclip btn-attachment" aria-hidden="true"></i>
					<button type="submit" class="chat-submit-btn"><i class="fas fa-paper-plane"></i></button>
				</div>
			</form>
		</div>
	</div>
</div>