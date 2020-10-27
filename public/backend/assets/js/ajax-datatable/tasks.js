(function($) {
	"use strict";
	
	var tasks_table = $('#tasks_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: ({
			url: _url + '/tasks/get_table_data',
			method: "POST",
			data: function (d) {

				d._token =  $('meta[name="csrf-token"]').attr('content');
                
                if($('select[name=project_id]').val() != ''){
	                d.project_id = $('select[name=project_id]').val();
	            }

                if($('select[name=assigned_user_id]').val() != ''){
	                d.assigned_user_id = $('select[name=assigned_user_id]').val();
	            }
                
                if($('select[name=task_status_id]').val() != null){
                	d.task_status_id = JSON.stringify($('select[name=task_status_id]').val());
                }
          
                if($('input[name=date_range]').val() != ''){
	                d.date_range = $('input[name=date_range]').val();
	            }
            },
			 error: function (request, status, error) {
				console.log(request.responseText);
			 }
		}),
		"columns" : [
			{ data : 'title', name : 'title' },
			{ data : 'project.name', name : 'project.name' },
			{ data : 'priority', name : 'priority' },
			{ data : 'status.title', name : 'status.title' },
			{ data : 'assigned_user.name', name : 'assigned_user.name' },
			{ data : 'start_date', name : 'start_date' },
			{ data : 'end_date', name : 'end_date' },
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
        tasks_table.draw();
    });

    $('#date_range').daterangepicker({
		autoUpdateInput: false,
		locale: {
		  format: 'YYYY-MM-DD',
		  cancelLabel: 'Clear'
		}
	});

	$('#date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        tasks_table.draw();
  	});

	$('#date_range').on('cancel.daterangepicker', function(ev, picker) {
	      $(this).val('');
	});
	
})(jQuery);

