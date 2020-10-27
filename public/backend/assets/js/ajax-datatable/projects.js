(function($) {
	"use strict";
	
	 var projects_table = $('#projects_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: ({
			url : _url + '/projects/get_table_data',
			method: "POST",
			data: function (d) {
				d._token =  $('meta[name="csrf-token"]').attr('content');
                
                if($('select[name=client_id]').val() != ''){
	                d.client_id = $('select[name=client_id]').val();
	            }

                if($('select[name=status]').val() != null){
                	d.status = JSON.stringify($('select[name=status]').val());
                }

            },
			 error: function (request, status, error) {
				console.log(request.responseText);
			 }
		}),
		"columns" : [
			{ data : 'name', name : 'name' },
			{ data : 'client.contact_name', name : 'client.contact_name' },
			{ data : 'status', name : 'status' },
			{ data : 'start_date', name : 'start_date' },
			{ data : 'end_date', name : 'end_date' },
			{ data : 'members.name', name : 'members.name' },
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
	}).on( 'init.dt', function () {
         $('[data-toggle="tooltip"]').tooltip();
    });

    $('.select-filter').on('change', function(e) {
        projects_table.draw();
    });
	
})(jQuery);

