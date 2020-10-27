
var pusher = new Pusher( _pusher_key , {
  authEndpoint: _pusher_end_point,
  cluster: _pusher_cluster,
  forceTLS: true
});

/*---------------* Pusher Trigger accessing channel *---------------*/
var presenceChannel = pusher.subscribe('presence-mychanel');
var groupChannel = pusher.subscribe('group-channel');

pusher.config.unavailable_timeout = 10000;

pusher.connection.bind('state_change', function(states) {
	var prevState = states.previous;
	var currState = states.current;
});
	
(function($){
	"use strict";	
		
	/*---------------* Pusher Trigger subscription succeeded *---------------*/
	presenceChannel.bind('pusher:subscription_error', function(status) {
		if (status == 408 || status == 503) {
			presenceChannel = pusher.subscribe('presence-mychanel');
		}
	});
	
	/*--------------* Get Message *----------*/
	presenceChannel.bind('message-event', function(data, metadata) {
		var me = presenceChannel.members.me;
		
		if(data.to == me.id && $("#receiver").val() == data.from){
			var sender = $("#user-"+ data.from +' .meta > .name').html();
			var sender_image = $("#user-"+ data.from +' .wrap > img').attr('src');
			
			$('<li class="replies"><img src="'+sender_image+'" alt="" /><p data-toggle="tooltip" title="'+ data.created_at +'">' + data.message + '</p></li>').appendTo($('.messages ul'));
			$('.contact.active .preview').html(data.message);
			$(".messages").stop().animate({ scrollTop: $(".messages")[0].scrollHeight}, 1000);
			
			//Mark As Read
			mark_as_read(data.from);
			
		}else if(data.to == me.id && $("#receiver").val() != data.from){

			//Notification Count
			if( _request_live_chat == "1" ){
				var sender = $("#user-"+ data.from +' .meta > .name').html();
				var notification_count = $("#user-"+ data.from +' .wrap > .notifications').html();
			
				if(notification_count == ''){
					notification_count = 1;
				}else{
					notification_count = parseInt(notification_count) + 1;
				}	

				$("#user-"+ data.from +' .wrap > .notifications').html(notification_count);
				
				$("#user-"+ data.from +' .wrap > .notifications').addClass('show').removeClass('hidden');
				$("#user-"+ data.from +' .preview').html(data.message);
			}else{
				if(data.from != user_id){
					var notification_count = $("#user-"+ data.from +' a > .widget-notification').html();
				
					if(notification_count == ''){
						notification_count = 1;
					}else{
						notification_count = parseInt(notification_count) + 1;
					}	
	
					$("#user-"+ data.from +' a > .widget-notification').html(notification_count);	
					$("#user-"+ data.from +' a > .widget-notification').addClass('show').removeClass('hidden');
				}else if(data.from == user_id && ! $(".chat-content").is(":visible")){

					var notification_count = $('.onchat-notification').html();
				
					if(notification_count == ''){
						notification_count = 1;
					}else{
						notification_count = parseInt(notification_count) + 1;
					}
					$(".onchat-notification").html(notification_count).addClass('show').removeClass('hidden');
				}else if(data.from == user_id && $(".chat-content").is(":visible")){
					//Mark As Read
					mark_as_read(data.from);
				}
			}
			
			//Global Notification
			var sidebar_notification_count = $('.chat-notification').html();
			sidebar_notification_count = parseInt(sidebar_notification_count) + 1;
			
			$('.chat-notification').html(sidebar_notification_count).addClass('show').removeClass('hidden');
			$('.widget-notification-main').html(sidebar_notification_count).addClass('show').removeClass('hidden');

			//Play Notification Sound
			play_chat_sound();	
		}
		
		
		if( _request_live_chat == "1" ){
			//Send to Top
			if(data.from == me.id){
				var $cloned = $('#user-' + data.to).clone();
				var $cloned_parent = $('#user-' + data.to).parent(); 
				$('#user-' + data.to).remove();
				$cloned.prependTo($cloned_parent);
			}else if(data.to == me.id){
				var $cloned = $('#user-' + data.from).clone();
				var $cloned_parent = $('#user-' + data.from).parent(); 
				$('#user-' + data.from).remove();
				$cloned.prependTo($cloned_parent);
			}
			
			//Show Attachment
			if( data.attachment == true){
				if(data.from == me.id && $("#receiver").val() == data.to){
					$('<li class="sent"><img src="'+ $("#profile-img").attr('src') +'" alt="" /><p data-toggle="tooltip" title="'+ data.created_at +'">' + data.message + '</p></li>').appendTo($('.messages ul'));
					$('.contact.active .preview').html('<span>' + $lang_you + ': </span>' + data.message);
					$(".messages").stop().animate({ scrollTop: $(".messages")[0].scrollHeight}, 1000);
				}
			}
		}
		
		
		/* Event for Chat Widget */
		if( _request_live_chat == "" ){
			if(data.from == me.id && user_id == data.to){
				if( data.attachment == true){
					$('<li class="send-msg"><p data-toggle="tooltip" title="'+ data.created_at +'">' + data.message + '</p></li>').appendTo($('#widget-chat-content ul'));
					$("#widget-chat-content").stop().animate({ scrollTop: $("#widget-chat-content")[0].scrollHeight}, 1000);
				}
			}else if(data.to == me.id && data.from == user_id){
				var sender_image = $("#user-"+ user_id +' a > img').attr('src');

				$('<li class="receive-msg"><img src="'+ sender_image +'"><p data-toggle="tooltip" title="'+ data.created_at +'">' + data.message + '</p></li>').appendTo($('#widget-chat-content ul'));
				$("#widget-chat-content").stop().animate({ scrollTop: $("#widget-chat-content")[0].scrollHeight}, 1000);   

			}
		}

	});
	
	/*--------------* Get Group Message *----------*/
	groupChannel.bind('group-message-event', function(data) {	
		var me = presenceChannel.members.me;

		if( data.group_members.indexOf(me.id) >= 0 && $("#group").val() == data.group_id && data.sender_id != me.id) {
			var sender_name = data.sender;
			var sender_image = $("#user-"+ data.sender_id +' .wrap > img').attr('src');
			
			$('<li class="replies"><img src="'+sender_image+'" alt="" data-toggle="tooltip" title="'+ sender_name +'"/><p data-toggle="tooltip" title="'+ data.created_at +'">' + data.message + '</p></li>').appendTo($('.messages ul'));
			$('.contact.active .preview').html(data.message);
			$(".messages").stop().animate({ scrollTop: $(".messages")[0].scrollHeight}, 1000);
			$('[data-toggle="tooltip"]').tooltip();
			
			//Mark As Read
			mark_as_group_read(data.group_id);
			
		}else if(data.group_members.indexOf(me.id) >= 0 && $("#group").val() != data.group_id && data.sender_id != me.id){
			//Notification Count
			if( _request_live_chat == "1" ){
				var sender = $("#group-"+ data.group_id +' .meta > .name').html();
				var notification_count = $("#group-"+ data.group_id +' .wrap > .notifications').html();
				
				if(notification_count == ''){
					notification_count = 1;
				}else{
					notification_count = parseInt(notification_count) + 1;
				}
				
				$("#group-"+ data.group_id +' .wrap > .notifications').html(notification_count);
				
				$("#group-"+ data.group_id +' .wrap > .notifications').addClass('show').removeClass('hidden');
				$("#group-"+ data.group_id +' .preview').html(data.message);

				//Send to Top
				var $cloned = $('#group-' + data.group_id).clone();
				var $cloned_parent = $('#group-' + data.group_id).parent(); 
				$('#group-' + data.group_id).remove();
				$cloned.prependTo($cloned_parent);
			}else{
				if(data.group_id != user_id){
					var notification_count = $("#group-"+ data.group_id +' a > .widget-notification').html();
					
					if(notification_count == ''){
						notification_count = 1;
					}else{
						notification_count = parseInt(notification_count) + 1;
					}
					
					$("#group-"+ data.group_id +' a > .widget-notification').html(notification_count);
					$("#group-"+ data.group_id +' a > .widget-notification').addClass('show').removeClass('hidden');
				}else if(data.group_id == user_id && ! $(".chat-content").is(":visible")){

					var notification_count = $('.onchat-notification').html();
				
					if(notification_count == ''){
						notification_count = 1;
					}else{
						notification_count = parseInt(notification_count) + 1;
					}
					$(".onchat-notification").html(notification_count).addClass('show').removeClass('hidden');
				}else if(data.group_id == user_id && $(".chat-content").is(":visible")){
					//Mark As Read
					mark_as_group_read(data.group_id);
				} 
			}
			
			
			//Global Notification
			var sidebar_notification_count = $('.chat-notification').html();
			sidebar_notification_count = parseInt(sidebar_notification_count) + 1;
			
			$('.chat-notification').html(sidebar_notification_count).addClass('show').removeClass('hidden');
			$('.widget-notification-main').html(sidebar_notification_count).addClass('show').removeClass('hidden');
			
			//Play Notification Sound
			play_chat_sound();

		}
		

		if( _request_live_chat == "1" ){	
			if(data.sender_id == me.id){
				//Send to Top
				var $cloned = $('#group-' + data.group_id).clone();
				var $cloned_parent = $('#group-' + data.group_id).parent(); 
				$('#group-' + data.group_id).remove();
				$cloned.prependTo($cloned_parent);
			}
			
			if( data.attachment == true){
				if(data.sender_id == me.id && $("#group").val() == data.group_id){
					var sender_name = data.sender;
					$('<li class="sent"><img src="'+ $("#profile-img").attr('src') +'" alt=""  data-toggle="tooltip" title="'+ sender_name +'"/><p data-toggle="tooltip" title="'+ data.created_at +'">' + data.message + '</p></li>').appendTo($('.messages ul'));
					$('.contact.active .preview').html('<span>' + $lang_you + ': </span>' + data.message);
					$(".messages").stop().animate({ scrollTop: $(".messages")[0].scrollHeight}, 1000);
				}
			}
		}
		
		/* Event for Chat Widget */
		if( _request_live_chat == "" ){
			if(data.sender_id == me.id && user_id != null){
				if( data.attachment == true){
					var profile_image = $("#my-profile-img").attr('src');
					$('<li class="send-msg"><img src="'+ profile_image +'" data-toggle="tooltip" title="'+ me.info.name +'"><p data-toggle="tooltip" title="'+ data.created_at +'">' + data.message + '</p></li>').appendTo($('#widget-chat-content ul'));
					$("#widget-chat-content").stop().animate({ scrollTop: $("#widget-chat-content")[0].scrollHeight}, 1000);
				}
			}else if(data.group_members.indexOf(me.id) >= 0 && data.sender_id != me.id && user_id != null){
				var sender_image = $("#user-" + data.sender_id + ' a > img').attr('src');
				var sender_name = $("#user-" + data.sender_id + ' a').data('name');

				$('<li class="receive-msg"><img src="'+ sender_image +'"  data-toggle="tooltip" title="'+ sender_name +'"><p data-toggle="tooltip" title="'+ data.created_at +'">' + data.message + '</p></li>').appendTo($('#widget-chat-content ul'));
				$("#widget-chat-content").stop().animate({ scrollTop: $("#widget-chat-content")[0].scrollHeight}, 1000);

			}
		}


	});	
	
})(jQuery);


function play_chat_sound(){
	var sound = document.getElementById("chatSound"); 
	if(localStorage.getItem('mute') == 0 || localStorage.getItem('mute') == null){
		sound.play();	 
	}			
}


/*-------------Mark as read--------------*/
function mark_as_read(sender_id){
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	$.ajax({
		method: 'POST',
		data: $("#chat-form").serialize(),
		url: _url + '/live_chat/mark_as_read/' + sender_id
	});
}

/*-------------Mark as read group message--------------*/
function mark_as_group_read(group_id){
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	$.ajax({
		method: 'POST',
		data: $("#chat-form").serialize(),
		url: _url + '/live_chat/mark_as_group_read/' + group_id
	});
}
