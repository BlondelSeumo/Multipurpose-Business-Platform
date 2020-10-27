@extends('layouts.app')

@section('content')
<style type="text/css">
#quotation-table td:nth-child(5){
	text-align: center !important;
}
</style>
<div class="row">
	<div class="col-12">
	
		<div class="card mt-2">
			<span class="d-none panel-title">{{ _lang('Quotation List') }}</span>

			<div class="card-body">
			  @php $currency = currency() @endphp
			  <table id="quotation-table" class="table table-bordered">
				<thead>
				  <tr>
					<th>{{ _lang('Quotation Number') }}</th>
					<th>{{ _lang('Quotation To') }}</th>
					<th>{{ _lang('Quotation Date') }}</th>
					<th class="text-right">{{ _lang('Grand Total') }}</th>
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
	$(function() {
        $('#quotation-table').DataTable({
            processing: true,
            serverSide: true,
			ajax: '{{ url('quotations/get_table_data') }}',
			"columns" : [
				{ data : "quotation_number", name : "quotation_number" },
				{ data : "contact_name", name : "contact_name" },
				{ data : "quotation_date", name : "quotation_date" },
				{ data : "grand_total", name : "grand_total" },
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
    });
</script>
@endsection




