@extends('layouts.app')

@section('content')
<style>
  .btn{margin-bottom: 2px !important;}
</style>
<div class="row">
	<div class="col-12">
		<div class="card">
			
			<span class="d-none panel-title">{{ _lang('Account Statement') }}</span>

			<div class="card-body">
			
				<div class="report-params">
					<form class="validate" method="post" action="{{ url('reports/account_statement/view') }}">
						<div class="row">
              				{{ csrf_field() }}
							<div class="col-md-3">
								<div class="form-group">
								<label class="control-label">{{ _lang('Select Account') }}</label>						
								  <select class="form-control select2" name="account" required>
										<option value="">{{ _lang('Select One') }}</option>
										{{ create_option("accounts","id","account_title",isset($account) ? $account : old('account'),array("company_id="=>company_id())) }}
								  </select>
								</div>
							</div>

						  <div class="col-md-2">
								<div class="form-group">
								<label class="control-label">{{ _lang('From') }}</label>						
								<input type="text" class="form-control datepicker" name="date1" id="date1" value="{{ isset($date1) ? $date1 : old('date1') }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
								<label class="control-label">{{ _lang('To') }}</label>						
								<input type="text" class="form-control datepicker" name="date2" id="date2" value="{{ isset($date2) ? $date2 : old('date2') }}" readOnly="true" required>
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group">
								<label class="control-label">{{ _lang('Transaction Type') }}</label>						
									<select class="form-control select2" name="trans_type" id="trans_type" required>
										<option value="all">{{ _lang('All') }}</option>
										<option value="dr">{{ _lang('Debit') }}</option>
										<option value="cr">{{ _lang('Credit') }}</option>									
									</select> 
								</div>
							</div>
							

							<div class="col-md-2">
								<button type="submit" class="btn btn-primary btn-xs mt-26">{{ _lang('View Report') }}</button>
							</div>
						</form>

					</div>
				</div><!--End Report param-->
                
				@php $date_format = get_company_option('date_format','Y-m-d'); @endphp
				
				<div class="report-header">
				   <h4>{{ isset($account) ? _lang('Account Statement Of').' '.$acc->account_title : _lang('Account Statement') }}</h4>
				   <h5>{{ isset($date1) ? date($date_format, strtotime($date1)).' '._lang('to').' '.date($date_format, strtotime($date2)) : '-------------  '._lang('to').'  -------------' }}</h5>
				</div>

				<table class="table table-bordered report-table">
					<thead>
						<th>{{ _lang('Date') }}</th>
						<th>{{ _lang('Description') }}</th>
						<th class="text-right">{{ _lang('Debit') }}</th>    
						<th class="text-right">{{ _lang('Credit') }}</th>       
					</thead>
					<tbody>
					 
					@if(isset($report_data))
						@php
							$currency = currency($acc->account_currency);
							$debit = 0;
							$credit = 0;
						@endphp
			
					@foreach($report_data as $report) 
                        @if( $report->debit == 0 && $report->credit == 0 )					
						   @php continue; @endphp
					    @endif
						<tr>
						   <td>{{ date($date_format, strtotime($report->date)) }}</td>
						   <td>{{ $report->note }}</td>
						   <td class="text-right">{{ $report->debit != 0 ? decimalPlace($report->debit, $currency) : "" }}</td>
						   <td class="text-right">{{ $report->credit != 0 ? decimalPlace($report->credit, $currency) : "" }}</td>
						</tr>
					 @php $debit += (float)$report->debit; $credit += (float)$report->credit;  @endphp
					 @endforeach
						<tr>
							<td></td>
							<td>{{ _lang('Total') }}</td>
							<td class="text-right"><b>{{ decimalPlace($debit, $currency) }}</b></td>
							<td class="text-right"><b>{{ decimalPlace($credit, $currency) }}</b></td>
						</tr>
					@endif
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
	"use strict";

	document.title = $(".panel-title").html();
	$("#trans_type").val("{{ isset($dr_cr) ? $dr_cr : 'all' }}");
	
})(jQuery);
</script>
@endsection


