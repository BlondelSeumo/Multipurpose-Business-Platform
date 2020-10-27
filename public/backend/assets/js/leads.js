
var php_date_format = ["Y-m-d", "d-m-Y", "d/m/Y","m-d-Y", "m.d.Y", "m/d/Y", "d.m.Y", "d/M/Y", "M/d/Y", "d M, Y"];
var js_date_format = ["YYYY-MM-DD", "DD-MM-YYYY", "DD/MM/YYYY","MM-DD-YYYY", "MM.DD.YYYY", "MM/DD/YYYY", "DD.MM.YYYY", "DD/MMM/YYYY", "MMM/DD/YYYY", "DD MMM, YYYY"];
	
(function($) {
    "use strict";

	$(".status").sortable({
		items: 'li',
		connectWith: ".lead-status",
		helper: "clone",
		appendTo: "#kanban-view",
		placeholder: "ui-state-highlight-card",
		revert: "invalid",
		stop: function (event, ui) {
			var status_id = ui.item.parent().data('lead-status-id');
			var lead_id = ui.item.data('lead-id');
			var link = _url + '/leads/update_lead_status/' + status_id + '/' + lead_id;	
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
			var link = _url + '/lead_statuses/update_lead_status_order/' + status_id + '/' + order;	
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
		$.ajax({
			url: _url + '/leads/load_more_lead/' + $(this).data('status-id') + '/' + $(this).data('lead-id'),
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

					var date_format = js_date_format[php_date_format.indexOf(_date_format)];

					rows += `<li data-lead-id="${element.id}">
								<div class="card">
									<div class="card-body">
										<img src="${_url}/public/uploads/profile/${element.assigned_user.profile_picture}" class="kanban-avatar" data-toggle="tooltip" data-placement="top" title="${element.assigned_user.name}">
										<a href="${_url}/leads/${element.id}" data-title="${element.name}" class="lead-title ajax-modal">${element.id} #-  ${element.name}</a>
										<div class="mt-2">
											<small>${$lang_source}: ${element.lead_source.title}</small><br>
											<small>${$lang_created}: ${moment(element.created_at).format(date_format)}</small>
										</div>
									</div>	
								</div>
							</li>`; 
					
					$(elem).data('lead-id',element.id);		
				});	
				$(elem).prev('.status').append(rows);	
			}
		});
		
	});
	
	
})(jQuery);		