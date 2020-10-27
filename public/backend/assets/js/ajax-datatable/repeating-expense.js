(function($) {
	"use strict";
	
	$('#repeating-expense-table').DataTable({
		processing: true,
		serverSide: true,
		ajax: _url + '/repeating_expense/get_table_data',
		"columns" : [
			{ data : "trans_date", name : "trans_date" },
			{ data : "account.account_title", name : "account.account_title" },
			{ data : "expense_type.name", name : "expense_type.name" },
			{ data : "amount", name : "amount" },
			{ data : "payee.contact_name", name : "payee.contact_name" },
			{ data : "status", name : "status" },
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

