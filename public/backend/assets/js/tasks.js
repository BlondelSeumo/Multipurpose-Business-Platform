(function($) {
    "use strict";

	$(".status").sortable({
		items: 'li',
		connectWith: ".lead-status",
		helper: "clone",
		appendTo: "#kanban-view",
		placeholder: "ui-state-highlight-task",
		revert: "invalid",
		stop: function (event, ui) {
			var status_id = ui.item.parent().data('task-status-id');
			var task_id = ui.item.data('task-id');
			var link = _url + '/tasks/update_task_status/' + status_id + '/' + task_id;	
			$.get(link);
		}
	});


	$("#kanban-view").sortable({
		helper: "clone",
		item: ".kanban-col",
		placeholder: "ui-state-highlight-kanban-col",
		stop: function (event, ui) {
			var index = 1;
			$('#kanban-view > .kanban-col').each(function(){
			   $(this).attr('data-order',index);
			   index++;
			});
			
			var status_id = ui.item.data('status-id');
			var order = ui.item.data('order');
			var link = _url + '/task_statuses/update_task_status_order/' + status_id + '/' + order;	
			$.get(link);
		},
	}).disableSelection();

	//Set Kanban Width
	var status_width = $("#kanban-view").children().length * 320;
	if(status_width > $("#kanban-view").width()){
		$("#kanban-view").css('min-width',status_width + 100);
	}

	//Click Load More
	$(document).on('click','.load-more',function(event){
		var elem = $(this);
		var button_text = "{{ _lang('Load More') }}";
		$.ajax({
			url: _url + '/tasks/load_more_task/' + $(this).data('status-id') + '/' + $(this).data('task-id'),
			beforeSend: function(data){
				$("#preloader").fadeIn();
			},success: function(data){
				$("#preloader").fadeOut();
				var json = JSON.parse(data);
				if(json.length == 0){
					$(elem).prop('disabled',true);
					$.toast({
						text: $lang_no_data_available,
						showHideTransition: 'slide',
						icon: 'error',
						position : 'top-right' 
					});				
					return;
				}
				var rows = '';
				$.each(json, function(index, element) {

					var assigned_user_name = element.assigned_user.length != 0 ? element.assigned_user.name : $lang_no_user_assigned;
					var assigned_user_picture = element.assigned_user.length != 0 ? element.assigned_user.profile_picture : 'default.png' ;
					
					rows += `<li data-task-id="${element.id}">
								<div class="card">
									<div class="card-body">
										<img src="${_url}/public/uploads/profile/${assigned_user_picture}" class="kanban-avatar" data-toggle="tooltip" data-placement="top" title="${assigned_user_name}">
										<a href="${_url}/tasks/${element.id}" data-title="${element.title}" class="lead-title ajax-modal">${element.id} #-  ${element.title}</a>
									</div>	
								</div>
							</li>`; 
					
					$(elem).data('task-id',element.id);		
				});	
				$(elem).prev('.status').append(rows);
			}
		});
		
		
		
	});

})(jQuery);	