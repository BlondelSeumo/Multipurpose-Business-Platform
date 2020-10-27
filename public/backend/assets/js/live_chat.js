var group_chat = false;
var load_more = true;
var limit = 20;
var offset = 0;


(function($){
	"use strict";

	/*---------------* Select User & Client from sidebar *---------------*/
	$(document).on('click','#staff-list .contact, #clients-list .contact',function(){
		group_chat = false;
		load_more = true;
		offset = 0;
		var id = $(this).data('id');
		$("#receiver").val(id);
		$("#group").val(null);
		$('.contact').removeClass('active');
		
		$(this).addClass('active');
		
		//Hidden Notification
		$("#user-"+ id +' .wrap > .notifications').html("0").addClass('hidden');
		
		
		var sender = $("#user-"+ id +' .meta > .name').html();
		var sender_image = $("#user-"+ id +' .wrap > img').attr('src');
		$(".contact-profile h5").html(sender);
		$(".contact-profile img").attr('src',sender_image);
			
		/* Fetch Latest Messages */
		$('.messages ul').html("");
		fetch_messages(id, limit, offset, true);
		
		//Hide Group Settings
		$(".group-settings").fadeOut();
		
	});

	/*---------------* Select Group from sidebar *---------------*/
	$(document).on('click','#group-list .contact',function(){
		group_chat = true;
		load_more = true;
		offset = 0;
		
		var group_id = $(this).data('group-id');
		$("#group").val(group_id);
		$("#receiver").val(null);
		$('.contact').removeClass('active');
		
		$(this).addClass('active');
		
		//Hidden Notification
		$("#group-"+ group_id +' .wrap > .notifications').html("0").addClass('hidden');

		
		var group = $("#group-"+ group_id +' .meta > .name').html();
		var group_image = $("#group-"+ group_id +' .wrap > img').attr('src');
		$(".contact-profile h5").html(group);
		$(".contact-profile img").attr('src',group_image);

		
		/* Fetch Latest Messages for group */
		$('.messages ul').html("");
		fetch_group_messages(group_id, limit, offset, true);
		
		//Display Group Settings
		$(".group-settings").fadeIn();
		group_settings();
		
		
	});

	$('#bottom-bar > button[data-toggle="tab"]').on('click', function(e){
		$("#contacts .tab-pane").removeClass('active in');
		$("#bottom-bar .nav-link").removeClass('active');
		$(this).addClass('active');
		var $target = $(this).data('target');
		$($target).addClass('active');
	});


	$(".messages").animate({ scrollTop: $(document).height() }, "fast");

	$(document).on('click','#btn-fullscreen',function(){
		$("#frame").toggleClass("chat-fullscreen");
		$("#btn-fullscreen > i").toggleClass("fa-search-plus");
		return false;	
	});

	$("#profile-img").click(function() {
		$("#status-options").toggleClass("active");
	});

	$(".expand-button").click(function() {
	  $("#profile").toggleClass("expanded");
		$("#contacts").toggleClass("expanded");
	});

	$("#status-options ul li").click(function() {
		$("#profile-img").removeClass();
		$("#status-online").removeClass("active");
		$("#status-away").removeClass("active");
		$("#status-busy").removeClass("active");
		$("#status-offline").removeClass("active");
		$(this).addClass("active");
		
		if($("#status-online").hasClass("active")) {
			$("#profile-img").addClass("online");
		} else if ($("#status-away").hasClass("active")) {
			$("#profile-img").addClass("away");
		} else if ($("#status-busy").hasClass("active")) {
			$("#profile-img").addClass("busy");
		} else if ($("#status-offline").hasClass("active")) {
			$("#profile-img").addClass("offline");
		} else {
			$("#profile-img").removeClass();
		};
		
		$("#status-options").removeClass("active");
	});


	/*---------------* Pusher Trigger subscription succeeded *---------------*/
	presenceChannel.bind('pusher:subscription_succeeded', function(members) {
	   chatMemberUpdate(members);
	});

	/*---------------* Pusher Trigger user connected *---------------*/
	presenceChannel.bind('pusher:member_added', function(member) {
	  addChatMember(member); 
	});

	/*---------------* Pusher Trigger user logout *---------------*/
	presenceChannel.bind('pusher:member_removed', function(members) {
	  removeChatMember(members);
	});

	/*------------ On Scroll Event-------------*/
	$(".messages").on( 'scroll', function(){
	   if($(".messages").scrollTop() == 0 && $('.messages ul li').length && load_more == true){ 
		   if(group_chat == false){
			 user_id = $("#receiver").val();
			 offset += 20;
			 fetch_messages(user_id, limit, offset, false);
		   }else{
			 group_id = $("#group").val();
			 offset += 20;
			 fetch_group_messages(group_id, limit, offset, false); 
		   }
	   }
	});


	/*---------- Fetch latest messages -----------*/
	function fetch_messages(user_id, limit, offset,scroll){
		if(scroll == false){
			var firstMsg = $('.messages ul li:first');
			var curOffset = $(firstMsg).offset().top - $('.messages').scrollTop(); 
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
				var sender_image = $("#user-"+ user_id +' .wrap > img').attr('src');

				var json = JSON.parse(data);
				if ( json.length == 0 ) {
					load_more = false; 
				}
				$.each(json, function(key, msg) {
					if(msg.from == me.id){
						$('<li class="sent" id="message_'+ msg.id +'"><img src="'+ $("#profile-img").attr('src') +'" alt="" /><p data-toggle="tooltip" title="'+ msg.created_at +'">' + msg.message + '</p></li>').prependTo($('.messages ul'));
					}else{
						$('<li class="replies" id="message_'+ msg.id +'"><img src="'+ sender_image +'" alt="" /><p data-toggle="tooltip" title="'+ msg.created_at +'">' + msg.message + '</p></li>').prependTo($('.messages ul'));
					}
				});
				
				if(scroll == true){
					$(".messages").stop().animate({ scrollTop: $(".messages")[0].scrollHeight}, 200);
					notification_count();
				}else{
					$(".messages").stop().animate({ scrollTop: firstMsg.offset().top - curOffset}, 500);
				}
				$('[data-toggle="tooltip"]').tooltip();
			}
		});
	}

	/*---------- Fetch Group messages -----------*/
	function fetch_group_messages(group_id, limit, offset, scroll){
		if(scroll == false){
			var firstMsg = $('.messages ul li:first');
			var curOffset = $(firstMsg).offset().top - $('.messages').scrollTop(); 
		}
		
		$.ajax({
			url: 'live_chat/get_group_messages/' + group_id + '/' + limit + '/' + offset,
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
					var sender_image = $("#user-"+ msg.sender_id +' .wrap > img').attr('src');

					if(msg.sender_id == me.id){
						var sender_name = $("#profile-name").html();
						$('<li class="sent" id="message_'+ msg.id +'"><img src="'+ $("#profile-img").attr('src') +'" alt="" data-toggle="tooltip" title="'+ sender_name +'"/><p data-toggle="tooltip" title="'+ msg.created_at +'">' + msg.message + '</p></li>').prependTo($('.messages ul'));
					}else{
						var sender_name = $("#user-"+ msg.sender_id +' .meta > .name').html();
						$('<li class="replies" id="message_'+ msg.id +'"><img src="'+ sender_image +'" alt="" data-toggle="tooltip" title="'+ sender_name +'"/><p data-toggle="tooltip" title="'+ msg.created_at +'">' + msg.message + '</p></li>').prependTo($('.messages ul'));
					}
				});
				
				if(scroll == true){
					$(".messages").stop().animate({ scrollTop: $(".messages")[0].scrollHeight}, 200);
					notification_count();
				}else{
					$(".messages").stop().animate({ scrollTop: firstMsg.offset().top - curOffset}, 500);
				}
				
				$('[data-toggle="tooltip"]').tooltip();
			}
		});
	}

	$(document).on('submit','#chat-form',function(){
	   if(group_chat == false){
		  newMessage();
	   }else{
		  newGroupMessage(); 
	   }
	   return false;
	});

	$(document).on("click",".btn-attachment",function(){
		$("#file").click();
	});

	/*---------Search Contact----------*/
	$(document).on('keyup','#st',function(){
		var search = $(this).val().toLowerCase();
		if(search == "" ){
			$('.contact').css("display","block");
		}
		$('.contact').each(function(i, obj) {
		   var name = $(this).find(".meta>.name").html().toLowerCase();
		   if( ! name.startsWith(search)){
			   $(this).css("display","none");
		   }else{
			   $(this).css("display","block");
		   }
		});
		
	});


	/*----------Create Chat Group------------*/
	$(document).on('submit','#create-group',function(){

		$.ajax({
			method: 'POST',
			url: $(this).attr('action'),
			data:  new FormData(this),
			contentType: false,
			cache: false,
			processData:false,
			beforeSend: function(){
				$('#preloader').fadeIn();
			},
			success: function(data){
				$('#preloader').fadeOut();
		
				if(data['result'] == true){			
					toast_alert( "success", data['message'] );
					$("#create-group")[0].reset();
					$("#group-members").val(null).trigger('change');				
					$("#main_modal").modal('hide');
				}
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		});
		return false;
	});

	/*----------Update Chat Group------------*/
	$(document).on('submit','#update-group',function(){

		$.ajax({
			method: 'POST',
			url: $(this).attr('action'),
			data:  new FormData(this),
			contentType: false,
			cache: false,
			processData:false,
			beforeSend: function(){
				$('#preloader').fadeIn();
			},
			success: function(data){
				$('#preloader').fadeOut();
		
				if(data['result'] == true){			
					toast_alert( "success", data['message'] );				
					$("#main_modal").modal('hide');
				}
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		});
		return false;
	});

	/*----------Delete Chat Group------------*/
	$(document).on('click','#btn-remove-group',function(){
		$.ajax({
			url: $(this).attr('href'),
			beforeSend: function(){
				$('#preloader').fadeIn();
			},
			success: function(data){
				$('#preloader').fadeOut();
				if(data['result'] == true){			
					toast_alert( "success", data['message'] );					
				}
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		});
		return false;
	});

	/*----------Left Group------------*/
	$(document).on('click','#btn-left-group',function(){
		$.ajax({
			url: $(this).attr('href'),
			beforeSend: function(){
				$('#preloader').fadeIn();
			},
			success: function(data){
				$('#preloader').fadeOut();
				if(data['result'] == true){			
					toast_alert( "success", data['message'] );					
					$("#group-" + data['group_id']).remove();
				}
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		});
		return false;
	});

	/*----------New Group Event------------*/
	groupChannel.bind('group-create-event', function(data) {
		var me = presenceChannel.members.me;
		var show = false;
		
		var members = "";
		$.each(data['group_members'], function(key, member) {
			members = members + ',&nbsp;' + member.name;
			if ( key === 0) {
			   members = member.name;
			}
			if(member.id == me.id){
				show = true;
			}
		});
		
		if(show == true){	
			var $new_group = `<li class="contact" id="group-${data['group'].id}" data-admin-id="${data['group'].created_by}" data-group-id="${data['group'].id}">
									<div class="wrap">
										<div class="group-img">${data['group'].img}</div>
										<div class="meta">
											<p class="name">${data['group'].name}</p>
											<p class="preview">${members}</p>
										</div>
									</div>
								</li>`;
			$("#group-list").prepend($new_group);
		}
	});

	/*---------- Group Update Event ------------*/
	groupChannel.bind('group-update-event', function(data) {
		var me = presenceChannel.members.me;
		var show = false;
		
		var members = "";
		$.each(data['group_members'], function(key, member) {
			members = members + ',&nbsp;' + member.name;
			if ( key === 0) {
			   members = member.name;
			}
			if(member.id == me.id){
				show = true;
			}
		});
		
		if(show == true){
			if($("#group-" + data['group'].id).length){
				var $updated_group = $("#group-" + data['group'].id);
				$updated_group.find('.wrap .group-img').html(data['group'].img);
				$updated_group.find('.meta .name').html(data['group'].name);
			}else{
				var $new_group = `<li class="contact" id="group-${data['group'].id}" data-group-id="${data['group'].id}">
									<div class="wrap">
										<div class="group-img">${data['group'].img}</div>
										<div class="meta">
											<p class="name">${data['group'].name}</p>
											<p class="preview">${data['last_message']}</p>
										</div>
									</div>
								</li>`;
				$("#group-list").prepend($new_group);
			}
		}else{
			$("#group-" + data['group'].id).remove();
		}
	});


	/*----------Delete Group Event------------*/
	groupChannel.bind('group-delete-event', function(group_id) {
		if($("#group-" + group_id).length){
			$("#group-" + group_id).remove();		
		}
	});


})(jQuery);	


function group_settings(){
	var me = presenceChannel.members.me;
	var admin = $("#group-list .active").data('admin-id');
	var group_id = $("#group-list .active").data('group-id');

	$("#btn-edit-group").attr('href', _url + "/live_chat/edit_group" + '/' + group_id);
	$("#btn-group-members").attr('href', _url + "/live_chat/view_group_members" + '/' + group_id);
	$("#btn-remove-group").attr('href', _url + "/live_chat/delete_group" + '/' + group_id);
	$("#btn-left-group").attr('href', _url + "/live_chat/left_group" + '/' + group_id);

	if(me.id == admin){
		$("#btn-edit-group").addClass('show').removeClass('hidden');
		$("#btn-remove-group").addClass('show').removeClass('hidden');
		$("#btn-left-group").addClass('hidden').removeClass('show');
	}else{
		$("#btn-edit-group").addClass('hidden').removeClass('show');
		$("#btn-remove-group").addClass('hidden').removeClass('show');
		$("#btn-left-group").addClass('show').removeClass('hidden');
	}	
}


function readFile(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {};
		$("#file_name").html(input.files[0].name);
		reader.readAsDataURL(input.files[0]);
	}
}

/*-------------Notification Count--------------*/
function notification_count(){
	$.ajax({
		method: "GET",
		url: _url + '/live_chat/notification_count',
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

/*---------------* chatMemberUpdate() place & update users on user page, unread messages notifications *---------------*/
function chatMemberUpdate(data) {
	  $('.contact .wrap span.contact-status').addClass('offline').removeClass('online');
	  
	  $.each(data.members, function(user_id, value) {	  
		$('#user-' + user_id + ' .wrap span.contact-status').addClass('online').removeClass('offline');	  
	  });
	  
	  /*-------- Sorting Staff List ---------*/
	  var staffs = [];
	  $("#staff-list li").each(function( index ) {
		   staffs.push($(this).data('order'));
	  });
	  staffs.sort(function(a, b){return b-a});
	  
	  $(staffs).each(function( index ) {
		   var $cloned = $('#staff-list li[data-order="'+ staffs[index] +'"]').clone();
		   $('#staff-list li[data-order="'+ staffs[index] +'"]').remove();
		   $cloned.prependTo("#staff-list");
	  });
	  
	  
	  /*-------- Sorting Client List ----------*/
	  var clients = [];
	  $("#clients-list li").each(function( index ) {
		   clients.push($(this).data('order'));
	  });
	  clients.sort(function(a, b){return b-a});
	  
	  $(clients).each(function( index ) {
		   var $cloned = $('#clients-list li[data-order="'+ clients[index] +'"]').clone();
		   $('#clients-list li[data-order="'+ clients[index] +'"]').remove();
		   $cloned.prependTo("#clients-list");
	  });
	  
	  /*-------- Sorting Group List ----------*/
	  var groups = [];
	  $("#group-list li").each(function( index ) {
		   groups.push($(this).data('order'));
	  });
	  groups.sort(function(a, b){return b-a});
	  
	  $(groups).each(function( index ) {
		   var $cloned = $('#group-list li[data-order="'+ groups[index] +'"]').clone();
		   $('#group-list li[data-order="'+ groups[index] +'"]').remove();
		   $cloned.prependTo("#group-list");
	  });

	  $("#chat-preloader").fadeOut(100);  
	
}


/*---------------* New chat members tracking / removing *---------------*/
function addChatMember(member) {
   $('#user-' + member.id + ' .wrap span.contact-status').addClass('online').removeClass('offline');
}

/*---------------* New chat members tracking / removing *---------------*/
function removeChatMember(member) {
	$('#user-' + member.id + ' .wrap span.contact-status').addClass('offline').removeClass('online');
	sortContact();
}

/*-----------Sort Contact List-----------*/
function sortContact() {
	//Sorting Staff List
	var staffs = [];
	$("#staff-list li").each(function( index ) {
		staffs.push($(this).data('order'));
	});
	staffs.sort(function(a, b){return b-a});
	  
	$(staffs).each(function( index ) {
		var $cloned = $('#staff-list li[data-order="'+ staffs[index] +'"]').clone();
		$('#staff-list li[data-order="'+ staffs[index] +'"]').remove();
		$cloned.prependTo("#staff-list");
	});
	  
	  
	//Sorting Client List
	var clients = [];
	$("#clients-list li").each(function( index ) {
		clients.push($(this).data('order'));
	});
	clients.sort(function(a, b){return b-a});
	  
	$(clients).each(function( index ) {
		var $cloned = $('#clients-list li[data-order="'+ clients[index] +'"]').clone();
		$('#clients-list li[data-order="'+ clients[index] +'"]').remove();
		$cloned.prependTo("#clients-list");
	});
	
	/*-------- Sorting Group List ----------*/
	var groups = [];
	$("#group-list li").each(function( index ) {
		groups.push($(this).data('order'));
	});
	groups.sort(function(a, b){return b-a});
	  
	$(groups).each(function( index ) {
		var $cloned = $('#group-list li[data-order="'+ groups[index] +'"]').clone();
		$('#group-list li[data-order="'+ groups[index] +'"]').remove();
		$cloned.prependTo("#group-list");
	});
}

/*------------* Send New Message *-------------*/
function newMessage() {
	message = $("#message").val();

	if($("#receiver").val() == ''){
		toast_alert( "error", $lang_please_select_a_contact_first );
		return false;
	}
	
	if($.trim(message) == '' && $("#file").val() == '') {
		return false;
	}
	
	
	if($("#file").val() == ''){	
		$('<li class="sent"><img src="'+ $("#profile-img").attr('src') +'" alt="" /><p data-toggle="tooltip" title="">' + message.replace(/(<([^>]+)>)/ig,"") + '</p> <i class="far fa-check-circle un-send"></i></li>').appendTo($('.messages ul'));
		$('.contact.active .preview').html('<span>'+ $lang_you +': </span>' + message.replace(/(<([^>]+)>)/ig,""));
		$(".messages").stop().animate({ scrollTop: $(".messages")[0].scrollHeight}, 1000);
	}
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	$.ajax({
		method: 'POST',
		url: _url + '/live_chat/send_message',
		data:  new FormData($("#chat-form")[0]),
		mimeType:"multipart/form-data",
		contentType: false,
		cache: false,
		processData:false,
		beforeSend: function(){
			$('#message').val(null);
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
				$("#file_name").html("");
				$("#file").val('');
				$('[data-toggle="tooltip"]').tooltip();
			}else{
				$(".un-send").parent().remove();
				$('#message').val(message);
				$("#file_name").html("");
				$("#file").val('');
				
				$.each(json['message'], function(key, msg) {
					toast_alert( "error", msg );					
				});
			}
		},
		error: function (request, status, error) {
			$('#message').val(message);
			console.log(request.responseText);
		}
	});
		
}


/*------------* Send New Group Message *-------------*/
function newGroupMessage() {
	message = $("#message").val();

	if($("#group").val() == ''){
		toast_alert( "error", $lang_please_select_a_group_first );
		return false;
	}
	
	if($.trim(message) == '' && $("#file").val() == '') {
		return false;
	}
	
	var me = presenceChannel.members.me;
	var sender_name = me.info.name;
	var sender_image = $('#profile-img').attr('src');
	
	if($("#file").val() == ''){	
		$('<li class="sent"><img src="'+ sender_image +'" alt="" data-toggle="tooltip" title="'+ sender_name +'"/><p data-toggle="tooltip" title="">' + message.replace(/(<([^>]+)>)/ig,"") + '</p> <i class="far fa-check-circle un-send"></i></li>').appendTo($('.messages ul'));
		$('.contact.active .preview').html('<span>'+ $lang_you +': </span>' + message.replace(/(<([^>]+)>)/ig,""));
		$(".messages").stop().animate({ scrollTop: $(".messages")[0].scrollHeight}, 1000);
	}
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	$.ajax({
		method: 'POST',
		url: _url + '/live_chat/send_group_message',
		data:  new FormData($("#chat-form")[0]),
		mimeType:"multipart/form-data",
		contentType: false,
		cache: false,
		processData:false,
		beforeSend: function(){
			if($("#file").val() != ''){
				$("#chat-preloader").fadeIn(100);
			}
			$('#message').val(null);
		},
		success: function(data){
			$("#chat-preloader").fadeOut(100);
			var json = JSON.parse(data);
			if(json['result'] == true){	
				$(".un-send").prev().html(json['data']['message']);			
				$(".un-send").prev().attr('title',json['data']['created_at']);			
				$(".un-send").remove();
				$("#file_name").html("");
				$("#file").val('');
				$('[data-toggle="tooltip"]').tooltip();
			}else{
				$(".un-send").parent().remove();
				$('#message').val(message);
				$("#file_name").html("");
				$("#file").val('');
				
				$.each(json['message'], function(key, msg) {
					toast_alert( "error", msg );					
				});
			}
		},
		error: function (request, status, error) {
			$('#message').val(message);
			console.log(request.responseText);
		}
	});
		
}