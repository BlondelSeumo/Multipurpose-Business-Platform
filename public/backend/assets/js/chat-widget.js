var group_chat = false;
var load_more = true;
var limit = 20;
var offset = 0;
var user_id = null;

(function($) {
	
    "use strict";
	
	$('.close-chat-box').on('click',function(){
		$('.chat-main').css('display','none');
		user_id = null;
		group_chat = false;
		$(".chat-online-users").css('display','block');
		notification_count();
	});
	
	$('.chat-header').on('click',function(){
		$('.chat-content').slideToggle();
		if($('.onchat-notification').is(":visible")){
			if(group_chat == false){
				mark_as_read(user_id);
			}else{
				mark_as_group_read(user_id);
			}
			$('.onchat-notification').html("0").addClass('hidden').removeClass('show');
		}
	});
	
	$('.chat-settings').on('click',function(e){
		 e.stopPropagation();
		$(".chat-settings-dropdown").fadeToggle(100);
	});
	
	$('.hide-online-user').on('click',function(e){
		e.stopPropagation();
		$('.chat-user-list').slideToggle();
	});

	
	$('.online-users-header').on('click',function(){
		$('.chat-user-list').slideToggle();
	});
	
	/*$('.chat-settings-dropdown a').on('click',function(e){
		e.stopPropagation();
		return false;
	});*/
	
	$('#mute-sound').on('click',function(e){
		e.stopPropagation();
		$(this).toggleClass('fa-volume-mute');
		$(this).toggleClass('fa-volume-up');
		
		if($(this).attr('class') == 'fas fa-volume-mute'){
			localStorage.setItem('mute', 1);
		}else{
			localStorage.setItem('mute', 0); 
		}

		return false;
	});
	
	//If already Mute
	if(localStorage.getItem('mute') == 1){
		$('#mute-sound').toggleClass('fa-volume-mute');
		$('#mute-sound').toggleClass('fa-volume-up');
	}
	
	
	
	/*---------------* Pusher Trigger subscription succeeded *---------------*/
	presenceChannel.bind('pusher:subscription_succeeded', function(members) {
		//Update Online Users
		var online_users = presenceChannel.members;	
		
		//Show Online Icon
		$.each(online_users.members, function(user_id, value) {	  
			$('#user-' + user_id + ' span.contact-status').addClass('online').removeClass('offline');	  
		});

		$('.online-user-count').html($(".chat-user-list .online").length);
			
	});
	
	/*---------------* Pusher Trigger user connected *---------------*/
	presenceChannel.bind('pusher:member_added', function(member) {
	   //Update Online Users
	   var online_users = presenceChannel.members;
	   /*if(online_users.count > 0){
		  $('.online-user-count').html(online_users.count-1);
	   }*/
	   $('#user-' + member.id + ' span.contact-status').addClass('online').removeClass('offline');
	   
	   $('.online-user-count').html($(".chat-user-list .online").length);
	});
	
	/*---------------* Pusher Trigger user logout *---------------*/
	presenceChannel.bind('pusher:member_removed', function(member) {
	   //Update Online Users
	   var online_users = presenceChannel.members;
	   /*if(online_users.count > 0){
		  $('.online-user-count').html(online_users.count-1);
	   }*/
	   $('#user-' + member.id + ' span.contact-status').addClass('offline').removeClass('online');
	   $('.online-user-count').html($(".chat-user-list .online").length);
	});
	
	//Click Widget Users
	$(document).on('click','.chat-user-list li > a',function(e){
		e.preventDefault();
		
		//Set Name
		$(".receiver").html($(this).data('name'));
		
		//Fetch messages
		load_more = true;
		offset = 0;
		user_id = $(this).data('id');
		$('#widget-chat-content ul').html("");
		$(this).find('.widget-notification').html('0').addClass('hidden').removeClass('show');

		if($(this).data('group') == 0){
			fetch_messages(user_id, limit, offset, true);
		}else{
			group_chat = true;
			fetch_group_messages(user_id, limit, offset, true); 
		}
		
		
		$(".chat-main").fadeToggle(300);
		$('.chat-content').css('display','block');
		$(".chat-online-users").fadeOut(300);
	});
	
	/*------------ On Scroll Event-------------*/
	$("#widget-chat-content").on( 'scroll', function(){
	   if($("#widget-chat-content").scrollTop() == 0 && $('#widget-chat-content ul li').length && load_more == true){ 
		   if(group_chat == false){
			 offset += 20;
			 fetch_messages(user_id, limit, offset, false);
		   }else{
			 //Group Message
			 offset += 20;
			 fetch_group_messages(user_id, limit, offset, false); 
		   }
	   }
	});
	
	//Send New Message
	$(document).on('submit','#widget-chat-form',function(){
	   if(group_chat == false){
		  newMessage();
	   }else{
		  newGroupMessage(); 
	   }
	   return false;
	});
	
	//Attachment Button
	$(document).on("click",".btn-attachment",function(){
		$("#file").click();
	});
	
})(jQuery);


function toast_alert( icon, message ){
	$.toast({
		text: message,
		showHideTransition: 'slide',
		icon: icon,
		position : 'top-right' 
	});
}

/*----------- Fetch latest messages -----------*/
function fetch_messages(user_id, limit, offset, scroll){
	if(scroll == false){
		var firstMsg = $('#widget-chat-content ul li:first');
		var curOffset = $(firstMsg).offset().top - $('#widget-chat-content').scrollTop(); 
    }
	
	if(user_id == null){
		$("#chat-preloader").fadeOut(100);
		return;
	}
	
	$.ajax({
		url: _url + '/live_chat/get_messages/' + user_id + '/' + limit + '/' + offset,
		beforeSend: function(){
			$("#chat-preloader").fadeIn(100);
		},
		success: function(data){
			$("#chat-preloader").fadeOut(100);
			var me = presenceChannel.members.me;
			var sender_image = $("#user-"+ user_id +' a > img').attr('src');

			var json = JSON.parse(data);
			if ( json.length == 0 ) {
				load_more = false; 
			}
			$.each(json, function(key, msg) {
				if(msg.from == me.id){
					$('<li class="send-msg" id="message_'+ msg.id +'"><p data-toggle="tooltip" data-placement="top" title="'+ msg.created_at +'">' + msg.message + '</p></li>').prependTo($('#widget-chat-content ul'));
				}else{
					$('<li class="receive-msg" id="message_'+ msg.id +'"><img src="'+ sender_image +'"><p data-toggle="tooltip" data-placement="top" title="'+ msg.created_at +'">' + msg.message + '</p></li>').prependTo($('#widget-chat-content ul'));
				}
			});
			
			if(scroll == true){
				$("#widget-chat-content").stop().animate({ scrollTop: $("#widget-chat-content")[0].scrollHeight}, 200);
			    notification_count();
			}else{
				$("#widget-chat-content").stop().animate({ scrollTop: firstMsg.offset().top - curOffset}, 500);
			}
			$('[data-toggle="tooltip"]').tooltip();
		}
	});
}


/*---------- Fetch Group messages -----------*/
function fetch_group_messages(group_id, limit, offset, scroll){
	if(scroll == false){
		var firstMsg = $('#widget-chat-content ul li:first');
		var curOffset = $(firstMsg).offset().top - $('#widget-chat-content').scrollTop(); 
    }
	
	$.ajax({
		url: _url + '/live_chat/get_group_messages/' + group_id + '/' + limit + '/' + offset,
		beforeSend: function(){
			$("#chat-preloader").fadeIn(100);
		},
		success: function(data){
			$("#chat-preloader").fadeOut(100);
			var me = presenceChannel.members.me;

			var json = JSON.parse(data);
			if ( json.length == 0 ) {
				load_more = false; 
			}
			$.each(json, function(key, msg) {
				
				if(msg.sender_id == me.id){
					var profile_image = $("#my-profile-img").attr('src');
					var sender_name = me.info.name;
                      
					$('<li class="send-msg" id="message_'+ msg.id +'"><img src="'+ profile_image +'" data-toggle="tooltip" title="'+ sender_name +'"><p data-toggle="tooltip" data-placement="top" title="'+ msg.created_at +'">' + msg.message + '</p></li>').prependTo($('#widget-chat-content ul'));
				}else{
					var sender_image = $("#user-"+ msg.sender_id +' a > img').attr('src');
				    var sender_name = $("#user-"+ msg.sender_id +' a').data('name');

					$('<li class="receive-msg" id="message_'+ msg.id +'"><img src="'+ sender_image +'" data-toggle="tooltip" title="'+ sender_name +'"><p data-toggle="tooltip" data-placement="top" title="'+ msg.created_at +'">' + msg.message + '</p></li>').prependTo($('#widget-chat-content ul'));
				}
			});
			
			if(scroll == true){
				$("#widget-chat-content").stop().animate({ scrollTop: $("#widget-chat-content")[0].scrollHeight}, 200);
			    notification_count();
			}else{
				$("#widget-chat-content").stop().animate({ scrollTop: firstMsg.offset().top - curOffset}, 500);
			}
			
			$('[data-toggle="tooltip"]').tooltip();
		}
	});
}

/*------------* Send New Message *-------------*/
function newMessage() {
	var message = $("#widget-message").val();

	if(user_id == null){
		//Command: toastr["error"]("{{ _lang('Please select a contact first') }}");
		toast_alert( "error", "{{ _lang('Please select a contact first') }}" );
		return false;
	}
	
	if($.trim(message) == '' && $("#file").val() == '') {
		return false;
	}
	
	
	if($("#file").val() == ''){	
		//$('<li class="sent"><img src="'+ $("#profile-img").attr('src') +'" alt="" /><p data-toggle="tooltip" title="">' + message.replace(/(<([^>]+)>)/ig,"") + '</p> <i class="far fa-check-circle un-send"></i></li>').appendTo($('.messages ul'));
        $('<li class="send-msg"><p data-toggle="tooltip" title="">' + message.replace(/(<([^>]+)>)/ig,"") + '</p> <i class="far fa-check-circle un-send"></i></li>').appendTo($('#widget-chat-content ul'));
		$("#widget-chat-content").stop().animate({ scrollTop: $("#widget-chat-content")[0].scrollHeight}, 1000);
	}
	
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
	
	var form_data = new FormData($("#widget-chat-form")[0]);
	form_data.append('to', user_id);
	
	$.ajax({
		method: 'POST',
		url: _url + '/live_chat/send_message',
		data:  form_data,
		mimeType:"multipart/form-data",
		contentType: false,
		cache: false,
		processData:false,
		beforeSend: function(){
			$('#widget-message').val(null);
			if($("#file").val() != ''){
				$("#chat-preloader").fadeIn(100);
			}
		},
		success: function(data){
			$("#chat-preloader").fadeOut(100);
			var json = JSON.parse(data);
			if(json['result'] == true){	
                $(".un-send").prev().html(json['data']['message']);			
                $(".un-send").prev().attr('title',json['data']['created_at']);			
				$(".un-send").remove();
				//$("#file_name").html("");
				$("#file").val('');
				$('[data-toggle="tooltip"]').tooltip();
			}else{
				$(".un-send").parent().remove();
				$('#widget-message').val(message);
				$("#file_name").html("");
				$("#file").val('');
				
				$.each(json['message'], function(key, msg) {
					//Command: toastr["error"](msg);	
                    toast_alert( "error", msg );					
				});
			}
		},
		error: function (request, status, error) {
			$('#widget-message').val(message);
			//console.log(request.responseText);
		}
	});
		
}


/*------------* Send New Group Message *-------------*/
function newGroupMessage() {
	message = $("#widget-message").val();

	if(user_id == null){
		//Command: toastr["error"]("{{ _lang('Please select a contact first') }}");
		toast_alert( "error", "{{ _lang('Please select a contact first') }}" );	
		return false;
	}
	
	if($.trim(message) == '' && $("#file").val() == '') {
		return false;
	}
	
	var me = presenceChannel.members.me;
	var sender_name = me.info.name;

	
	if($("#file").val() == ''){	
	    var profile_image = $("#my-profile-img").attr('src');
	    $('<li class="send-msg"><img src="'+ profile_image +'" data-toggle="tooltip" title="'+ sender_name +'"><p data-toggle="tooltip" title="">' + message.replace(/(<([^>]+)>)/ig,"") + '</p> <i class="far fa-check-circle un-send"></i></li>').appendTo($('#widget-chat-content ul'));
		$("#widget-chat-content").stop().animate({ scrollTop: $("#widget-chat-content")[0].scrollHeight}, 1000);
	}
	
	var form_data = new FormData($("#widget-chat-form")[0]);
	form_data.append('group', user_id);
	
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
	
	$.ajax({
		method: 'POST',
		url: _url + '/live_chat/send_group_message',
		data: form_data,
		mimeType:"multipart/form-data",
		contentType: false,
		cache: false,
		processData:false,
		beforeSend: function(){
			if($("#file").val() != ''){
				$("#chat-preloader").fadeIn(100);
			}
			$('#widget-message').val(null);
		},
		success: function(data){
			$("#chat-preloader").fadeOut(100);
			var json = JSON.parse(data);
			if(json['result'] == true){	
                $(".un-send").prev().html(json['data']['message']);			
                $(".un-send").prev().attr('title',json['data']['created_at']);			
				$(".un-send").remove();
				//$("#file_name").html("");
				$("#file").val('');
				$('[data-toggle="tooltip"]').tooltip();
			}else{
				$(".un-send").parent().remove();
				$('#message').val(message);
				//$("#file_name").html("");
				$("#file").val('');
				
				$.each(json['message'], function(key, msg) {
					//Command: toastr["error"](msg);
                    toast_alert( "error", msg );					
				});
			}
		},
		error: function (request, status, error) {
			$('#widget-message').val(message);
			console.log(request.responseText);
		}
	});
		
}

/*-------------Notification Count--------------*/
function notification_count(){
	$.ajax({
		method: "GET",
		url:  _url + '/live_chat/notification_count',
		success: function(data){		
			if(data != '0'){
				$(".chat-notification").html(data);
				$(".widget-notification-main").html(data);
			}else{
				$(".chat-notification").html("0").addClass('hidden').removeClass('show');
				$(".widget-notification-main").html("0").addClass('hidden').removeClass('show');
			}
		},
		error: function (request, status, error) {
			console.log(request.responseText);
		}
	});
}

function readFile(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {};
		//$("#file_name").html(input.files[0].name);
		reader.readAsDataURL(input.files[0]);
		$("#widget-chat-form").submit();
	}
}