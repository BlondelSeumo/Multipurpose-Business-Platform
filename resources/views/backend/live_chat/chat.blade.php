@extends('layouts.app')

@section('content')

@if(Auth::user()->user_type == 'client')
<style>
	#frame #sidepanel #bottom-bar button {
		width: 50% !important;
	}
</style>
@endif

<link href="{{ asset('public/backend/assets/css/chat.css?v=1.2') }}" rel="stylesheet" type="text/css" />

<!--Chat Box-->
<div class="row">
  <!-- Panel 1 -->
  <div class="col-12">
     <span class="panel-title d-none">{{ _lang('Messenger') }}</span>
		
		<div id="frame" class="chat-fullscreen">
			<div id="sidepanel">
				<div id="profile">
					<div class="wrap">
						<img id="profile-img" src="{{ Auth::user()->profile_picture != "" ? asset('public/uploads/profile/'.Auth::user()->profile_picture) :  asset('public/images/avatar.png') }}" class="online" alt="" />
						<p id="profile-name">{{ Auth::user()->name }}</p>
					</div>
				</div>
				<div id="search">
					<label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
					<input type="text" id="st" placeholder="{{ _lang('Search Contacts') }}" />
				</div>
				@php 
				 $chat_order = get_chat_order();
				 $group_chat_order = get_group_chat_order();				
				@endphp
				
				<div id="contacts" class="tab-content">
					<div id="staff" class="tab-pane active">
						<ul id="staff-list">
						   @foreach($staffs as $staff)
								<li class="contact" id="user-{{ $staff->id }}" data-id="{{ $staff->id }}" data-order="{{ array_key_exists($staff->id,$chat_order) ? $chat_order[$staff->id] : $staff->id * 45 }}">
									<div class="wrap">
										<span class="notifications {{ $staff->unread_message_count > 0 ? 'show' : 'hidden' }}">{{ $staff->unread_message_count }}</span>
										<span class="contact-status offline"></span>
										<img src="{{ $staff->profile_picture != "" ? asset('public/uploads/profile/'.$staff->profile_picture) :  asset('public/images/avatar.png') }}" alt="" />
										<div class="meta">
											<p class="name">{{ $staff->name }}</p>
											{!! clean(get_last_message($staff->id)) !!}
										</div>
									</div>
								</li>
							@endforeach
						</ul>
					</div>
						
					<div id="groups" class="tab-pane">
					    <a href="{{ url('live_chat/create_group') }}" data-title="{{ _lang('Create Chat Group') }}" class="btn-create-group btn-block ajax-modal">{{ _lang('Create New Group') }}</a>
					    
						<ul id="group-list">
						   @foreach($chat_groups as $chat_group)
						        <li class="contact" id="group-{{ $chat_group->id }}" data-group-id="{{ $chat_group->id }}" data-admin-id="{{ $chat_group->created_by }}" data-order="{{ array_key_exists($chat_group->id,$group_chat_order) ? $group_chat_order[$chat_group->id] : $chat_group->id * 45 }}">
									<div class="wrap">
									    @php $msg_count = group_message_count($chat_group->id) @endphp
										<span class="notifications {{ $msg_count > 0 ? 'show' : 'hidden' }}">{{ $msg_count }}</span>
										<div class="group-img">{{ get_initials($chat_group->name) }}</div>
										<div class="meta">
											<p class="name">{{ $chat_group->name }}</p>
											<p class="preview">{!! clean(get_last_group_message($chat_group->id)) !!}</p>
										</div>
									</div>
								</li>
						   @endforeach
						</ul>
					</div>
					
					@if(Auth::user()->user_type != 'client')					
					<div id="clients" class="tab-pane">
						<ul id="clients-list"> 
							@foreach($clients as $client)
								<li class="contact" id="user-{{ $client->id }}" data-id="{{ $client->id }}" data-order="{{ array_key_exists($client->id, $chat_order) ? $chat_order[$client->id] : $client->id * 45 }}">
									<div class="wrap">
										<span class="notifications {{ $client->unread_message_count > 0 ? 'show' : 'hidden' }}">{{ $client->unread_message_count }}</span>
										<span class="contact-status offline"></span>
										<img src="{{ $client->profile_picture != "" ? asset('public/uploads/profile/'.$client->profile_picture) :  asset('public/images/avatar.png') }}" alt="" />
										<div class="meta">
											<p class="name">{{ $client->name }}</p>
											{!! clean(get_last_message($client->id)) !!}
										</div>
									</div>
								</li>
							@endforeach	
						</ul>
					</div>
					@endif
				</div>
				<div id="bottom-bar">
					<button class="nav-link active" data-toggle="tab" data-target="#staff"><i class="fa fa-user"></i> <span>{{ _lang('Staff') }}</span></button>
					<button class="nav-link" data-toggle="tab" data-target="#groups"><i class="fa fa-users"></i> <span>{{ _lang('Group') }}</span></button>
					@if(Auth::user()->user_type != 'client')
						<button class="nav-link" data-toggle="tab" data-target="#clients"><i class="fa fa-user-circle"></i> <span>{{ _lang('Clients') }}</span></button>
				    @endif
				</div>
			</div>
			<div class="content">
			    <div id="chat-preloader">    
					<i class="fas fa-circle-notch fa-pulse"></i>
				</div>
				<div class="contact-profile">
					<img src="{{ asset('public/images/avatar.png') }}" alt="" />
					<h5>{{ _lang('Select Contact') }}</h5>
					<!--<div class="social-media">
						<i class="fab fa-facebook"></i>
						<i class="fab fa-twitter"></i>
						<i class="fab fa-instagram"></i>
					</div>-->
					<button class="btn btn-dark btn-xs float-right" id="btn-fullscreen"><i class="fa fa-search-minus"></i></button>
					<div class="group-settings float-right">
					    <div class="dropdown">
						  <button class="btn-group-settings btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown"><i class="fas fa-users-cog"></i>
						  <i class="fa fa-angle-down"></i></button>
						  <div class="dropdown-menu">
							<a href="" class="dropdown-item ajax-modal" id="btn-group-members" data-title="{{ _lang('Members') }}">{{ _lang('Group Members') }}</a>
							<a href="" class="dropdown-item hidden" id="btn-left-group">{{ _lang('Left Group') }}</a>
							<a href="" class="dropdown-item hidden ajax-modal" id="btn-edit-group" data-title="{{ _lang('Edit Group') }}">{{ _lang('Edit Group') }}</a>
							<a href="" class="dropdown-item hidden" id="btn-remove-group">{{ _lang('Remove Group') }}</a>
						  </div>
						</div>
					</div>
				</div>
				<div class="messages">
					<ul>
					    <li class="select-chat-user">
						    <i class="typcn typcn-messages"></i><br>
							{{ _lang('Welcome').', '.Auth::user()->name }}<br>
							{{ _lang('Ready to Start Your Conversation') }}
						</li>
					</ul>
				</div>
				<div class="message-input">
					<div class="wrap">
						<form id="chat-form" method="post" autocomplete="off">
							<input type="hidden" name="to" id="receiver">
							<input type="hidden" name="group" id="group">
							<input type="text" name="message" id="message" placeholder="Write your message..." />
							
							<input type="file" name="file" id="file" class="d-none" onchange="readFile(this);">
							<span id="file_name"></span>
							<button type="button" class="button btn-attachment"><i class="fa fa-paperclip" aria-hidden="true"></i></button>
							<button type="submit" class="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
						</form>
					</div>
				</div>
			</div>
		</div>
  </div>
  <!-- End Panel 1 -->
</div>

@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/live_chat.js?v=1.0') }}" defer></script>
@endsection
