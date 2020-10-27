
(function($) {
    "use strict";
	
    var current_element;
	
	var clipboard = new ClipboardJS('#data > ul > li');
	clipboard.on('success', function(e) {
		toast_alert( "success", 'Copied' );
	});
	
	$('[data-toggle="tooltip"]').tooltip();
		
	$(document).on('click','#btn-preview, #btn-editor',function(){
		$("#invoice-canvas div > i.fa-edit").toggle();
		$("#invoice-canvas div > i.fa-trash-alt").toggle();
		$("#invoice-canvas div").toggleClass('toggle-preview');
		$('#btn-preview').toggleClass('d-none');
		$('#btn-editor').toggleClass('d-none');
	});
	
	$( "#invoice-canvas" ).droppable({
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		greedy: true,
		drop: function (event, ui) {
			var droppable = $(this);
			var draggable = ui.draggable;
			var element = draggable.data('element');
			if(typeof element  !== "undefined"){
				$.ajax({
					url: _url + '/invoice_templates/element/' + element,
					beforesend: function(){
					   $("#preloader").fadeIn();
					},success: function(data){
					   $("#preloader").fadeIn();
					   var json = JSON.parse(data);
					   var option_fields = json['option_fields'];
					   
					   $(droppable).append(json['element']);
					   var item = $(droppable).children().last();
					   new_droppable(item);
					   
					   $( item ).append(option_fields);
					   $( item ).attr('data-element-type', element);
					   $( item ).draggable({
							revert: "invalid",
							containment: "document",
							helper: "clone",
							cursor: "pointer",
							start  : function(event, ui){
								$(ui.helper).addClass("ui-helper");
							},
					   });
					   //$( item ).sortable();
					}
				});
			}else if(draggable.data('element-type') !== "undefined"){
				$(droppable).append(draggable);
			}
		}
	});
	
	//$( "#invoice-canvas" ).sortable();
	
	$( "#components ul > li" ).draggable({
		revert: "invalid",
		containment: "document",
		helper: "clone",
		cursor: "move",
		start  : function(event, ui){
			$(ui.helper).addClass("ui-helper");
		},
	});
	

	//Edit Element Click
	$(document).on('click','#invoice-canvas div > i.fa-edit',function(){
		current_element = $(this).parent();
		var form_field = '<form class="submit-element-settings" autocomplete="off" method="post"><div class="row">';
		form_field += $(this).parent().find('form').html();
		form_field += '</div></form>';
		$("#secondary_modal .modal-title").html("Element Settings");
		$("#secondary_modal .modal-body").html(form_field);
		$("#secondary_modal").modal('show');
		
	});
	
	//Remove Element Click
	$(document).on('click','#invoice-canvas div > i.fa-trash-alt',function(){
		Swal.fire({
		  title: $lang_alert_title,
		  text: $lang_alert_message,
		  icon: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: $lang_confirm_button_text,
		  cancelButtonText: $lang_cancel_button_text
		}).then((result) => {
		  if (result.value) {
			 $(this).parent().fadeOut(300, function(){ $(this).remove();});
		  }
		});
		
	});
	
	//Submit Settings Form
	$(document).on('submit','.submit-element-settings',function(event){
		event.preventDefault();
		var element_type = current_element.data('element-type');
		var form_data = $(this).serializeArray();
        
		$.each( form_data, function( key, field ) {
			var field_name = field['name'];
			var field_value = field['value'];
		  
		    var change_class = $('.' + field_name).attr('data-change-class');
		    var change_action = $('.' + field_name).attr('data-change-action');
			
			var functions = change_action.split("_");
			
			if(change_class != ''){

				if(change_action == 'addClass'){
					$(current_element).find(change_class).removeClass($(current_element).find('.' + field_name).val());	
				}
				
				if(functions.length > 1){
					$(current_element).find(change_class)[functions[0]](functions[1],field_value);
				}else{
					$(current_element).find(change_class)[functions[0]](field_value);
				}
				
			}else{
				if(change_action == 'addClass'){
					$(current_element).removeClass($(current_element).find('.' + field_name).val());
				}
				
				if(functions.length > 1){
					$(current_element)[functions[0]](functions[1],field_value);
				}else{
					$(current_element)[functions[0]](field_value);
				}
			}
				
			if ($($(current_element).find('form').find("." + field_name)).is('input')){
				$(current_element).find('form').find("." + field_name).attr('value', field_value);	
			}else if ($($(current_element).find('form').find("." + field_name)).is('textarea')){
				$(current_element).find('form').find("." + field_name).html(field_value);	
			}else{
				$(current_element).find('form').find("." + field_name).find(':selected').removeAttr('selected');
				$(current_element).find('form').find("." + field_name +' option[value='+ field_value +']').attr('selected','selected');
			}		
		});
		
		$("#secondary_modal").modal('hide');
		
	});
	
	//Store Invoice Template
	$(document).on('submit','#store_invoice_template',function(event){
		event.preventDefault();
		
		var body_code   = get_body_code();
		var editor_code = get_editor_code();
		
		$.ajax({
			url:    $(this).attr('action'),
			method: 'POST',
			data: {_token: $('meta[name="csrf-token"]').attr('content'), name: $("#template_name").val(), body: body_code, editor: editor_code},
			beforesend: function(){
				
			},success: function(data){
				var json = JSON.parse(JSON.stringify(data));
				if(json['result'] == 'success'){
					$("#main_alert > span.msg").html(json['message']);
					$("#main_alert").addClass("alert-success").removeClass("alert-danger");
					$("#main_alert").css('display','block');
				}else{
					if(Array.isArray(json['message'])){
						$("#main_alert > span.msg").html("");
						$("#main_alert").addClass("alert-danger").removeClass("alert-success");
						
						jQuery.each( json['message'], function( i, val ) {
						   $("#main_alert > span.msg").append("<br><i class='typcn typcn-delete'></i> " + val);	
						});	
						$("#main_alert").css('display','block');
					}else{
						$("#main_alert > span.msg").html("");
						$("#main_alert").addClass("alert-danger").removeClass("alert-success");
						$("#main_alert > span.msg").html("<p>" + json['message'] + "</p>");		
						$("#main_alert").css('display','block');
					}
				}
			}
		});
	});
	
	//Update Invoice Template
	$(document).on('click','#update_invoice_template',function(){

		var body_code   = get_body_code();
		var editor_code = get_editor_code();
		
		$.ajax({
			url:    $("#action").val(),
			method: 'PATCH',
			data: {_token: $('meta[name="csrf-token"]').attr('content'), name: $("#template_name").val(), body: body_code, editor: editor_code},
			beforesend: function(){
				
			},success: function(data){
				var json = JSON.parse(JSON.stringify(data));
				if(json['result'] == 'success'){
					$("#main_alert > span.msg").html(json['message']);
					$("#main_alert").addClass("alert-success").removeClass("alert-danger");
					$("#main_alert").css('display','block');
				}else{
					if(Array.isArray(json['message'])){
						$("#main_alert > span.msg").html("");
						$("#main_alert").addClass("alert-danger").removeClass("alert-success");
						
						jQuery.each( json['message'], function( i, val ) {
						   $("#main_alert > span.msg").append('<i class="far fa-times-circle"></i> ' + val + '<br>');	
						});	
						$("#main_alert").css('display','block');
					}else{
						$("#main_alert > span.msg").html("");
						$("#main_alert").addClass("alert-danger").removeClass("alert-success");
						$("#main_alert > span.msg").html("<p>" + json['message'] + "</p>");		
						$("#main_alert").css('display','block');
					}
				}
			}
		});
	});


})(jQuery);

function new_droppable(elem){
	$( elem ).droppable({
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		greedy: true,
		drop: function (event, ui) {
			var droppable = $(this);
			var draggable = ui.draggable;
			var element = draggable.data('element');
			if(typeof element  !== "undefined"){
				$.ajax({
					url: _url + '/invoice_templates/element/' + element,
					beforesend: function(){
					   $("#preloader").fadeIn();
					},success: function(data){
					   $("#preloader").fadeIn();
					   var json = JSON.parse(data);
					   var option_fields = json['option_fields'];
					   
					   $(droppable).append(json['element']);
					   var item = $(droppable).children().last();
					   new_droppable(item);
					   
					   $( item ).append(option_fields);
					   $( item ).attr('data-element-type', element);
                       $( item ).draggable({
							revert: "invalid",
							containment: "document",
							helper: "clone",
							cursor: "pointer",
							start  : function(event, ui){
								$(ui.helper).addClass("ui-helper");
							},
					   });
					   //$( elem ).sortable();
					}
				});
			}else if(draggable.data('element-type') !== "undefined"){
				$(droppable).append(draggable);
			}
		}
	});
}

function get_body_code(){
	var canvas = $("#invoice-canvas").clone();
	canvas.find("form").remove();
	canvas.find(".fa-trash-alt").remove();
	canvas.find(".fa-edit").remove();
	return $.trim(canvas.html());
}

function get_editor_code(){
	var canvas = $("#invoice-canvas");
	return canvas.html();
}

