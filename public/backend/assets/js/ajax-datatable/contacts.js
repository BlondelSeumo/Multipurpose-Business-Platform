(function($) {
	"use strict";
	
	$('#contacts-table').DataTable({
		processing: true,
		serverSide: true,
		ajax: _url + '/contacts/get_table_data',
		"columns" : [
				{ data : "contact_image", name : "contact_image" },
				{ data : "contact_name", name : "contact_name" },
				{ data : "profile_type", name : "profile_type" },
				{ data : "contact_email", name : "contact_email" },
				{ data : "contact_phone", name : "contact_phone" },
				{ data : "group.name", name : "group.name" },
				{ data : "action", name : "action" },
		],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,	
		"ordering": false,
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
})(jQuery);

