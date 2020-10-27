(function($) {
	"use strict";
	
	 var leads_table = $('#leads_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: ({
			url : _url + '/leads/get_table_data',
			method: "POST",
			data: function (d) {
				d._token =  $('meta[name="csrf-token"]').attr('content');
                
                if($('select[name=assigned_user_id]').val() != ''){
	                d.assigned_user_id = $('select[name=assigned_user_id]').val();
	            }
                
                if($('select[name=lead_status_id]').val() != null){
                	d.lead_status_id = JSON.stringify($('select[name=lead_status_id]').val());
                }

                if($('select[name=lead_source_id]').val() != ''){
	                d.lead_source_id = $('select[name=lead_source_id]').val();
	            }

                if($('select[name=country]').val() != ''){
	                d.country = $('select[name=country]').val();
	            }
            },
			 error: function (request, status, error) {
				console.log(request.responseText);
			 }
		}),
		"columns" : [
			{ data : 'name', name : 'name' },
			{ data : 'company_name', name : 'company_name' },
			{ data : 'email', name : 'email' },
			{ data : 'phone', name : 'phone' },
			{ data : 'lead_status.title', name : 'lead_status.title' },
			{ data : 'lead_source.title', name : 'lead_source.title' },
			{ data : 'assigned_user.name', name : 'assigned_user.name' },
			{ data : "action", name : "action" },
		],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,	
		"ordering": false,
		"searching": false,
		"language": {
		   "decimal":        "",
		   "emptyTable":     $lang_no_data_found,
		   "info":           $lang_showing + " _START_ " + $lang_to + " _END_ " + $lang_of + " _TOTAL_ " + $lang_entries,
		   "infoEmpty":      $lang_showing_0_to_0_of_0_entries,
		   "infoFiltered":   "(filtered from _MAX_ total entries)",
		   "infoPostFix":    "",
		   "thousands":      ",",
		   "lengthMenu":     $lang_show + " _MENU_ " + $lang_entries,
		   "loadingRecords": $lang_loading,
		   "processing":     $lang_processing,
		   "search":         $lang_search,
		   "zeroRecords":    $lang_no_matching_records_found,
		   "paginate": {
			  "first":      $lang_first,
			  "last":       $lang_last,
			  "next":       $lang_next,
			  "previous":   $lang_previous
		   }
		}
	});
	
	$('.select-filter').on('change', function(e) {
        leads_table.draw();
    });
	
})(jQuery);

