@extends('layouts.app')

@section('content')
<style type="text/css">
#invoice-table td:nth-child(5), #invoice-table td:nth-child(6){
	text-align: center !important;
}
</style>

<div class="row">
	<div class="col-12">
	
		<div class="card mt-2">
			<span class="panel-title d-none">{{ _lang('Invoice List') }}</span>

			<div class="card-body">
			  @php $currency = currency() @endphp
			  <table id="invoice-table" class="table table-bordered">
				<thead>
				  <tr>
					<th>{{ _lang('Invoice Number') }}</th>
					<th>{{ _lang('Invoice To') }}</th>
					<th>{{ _lang('Due Date') }}</th>
					<th class="text-right">{{ _lang('Grand Total') }}</th>
					<th class="text-center">{{ _lang('Status') }}</th>
					<th class="text-center">{{ _lang('Action') }}</th>
				  </tr>
				</thead>
				<tbody>

				</tbody>
			  </table>
			</div>
		</div>
	</div>
</div>

@endsection


@section('js-script')
<script>
	(function($) {
        $('#invoice-table').DataTable({
            processing: true,
            serverSide: true,
			ajax: '{{ url('invoices/get_table_data') }}',
			"columns" : [
				{ data : "invoice_number", name : "invoice_number" },
				{ data : "contact_name", name : "contact_name" },
				{ data : "due_date", name : "due_date" },
				{ data : "grand_total", name : "grand_total" },
				{ data : "status", name : "status" },
				{ data : "action", name : "action" },
			],
			responsive: true,
			"bStateSave": true,
			"bAutoWidth":false,	
			"ordering": false,
			"language": {
				"decimal":        "",
				"emptyTable":     "{{ _lang('No Data Found') }}",
				"info":           "{{ _lang('Showing') }} _START_ {{ _lang('to') }} _END_ {{ _lang('of') }} _TOTAL_ {{ _lang('Entries') }}",
				"infoEmpty":      "{{ _lang('Showing 0 To 0 Of 0 Entries') }}",
				"infoFiltered":   "(filtered from _MAX_ total entries)",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     "{{ _lang('Show') }} _MENU_ {{ _lang('Entries') }}",
				"loadingRecords": "{{ _lang('Loading...') }}",
				"processing":     "{{ _lang('Processing...') }}",
				"search":         "{{ _lang('Search') }}",
				"zeroRecords":    "{{ _lang('No matching records found') }}",
				"paginate": {
					"first":      "{{ _lang('First') }}",
					"last":       "{{ _lang('Last') }}",
					"next":       "{{ _lang('Next') }}",
					"previous":   "{{ _lang('Previous') }}"
				}
			} 
        });
    })(jQuery);
</script>
@endsection


