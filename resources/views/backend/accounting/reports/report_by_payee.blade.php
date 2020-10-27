@extends('layouts.app')

@section('content')
<style>
  .btn{margin-bottom: 2px !important;}
</style>
<div class="row">
	<div class="col-12">
		<div class="card">
			
			<span class="d-none panel-title">{{ _lang('Report By Payee') }}</span>

			<div class="card-body">
			
				<div class="report-params">
					<form class="validate" method="post" action="{{ url('reports/report_by_payee/view') }}">
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
								<label class="control-label">{{ _lang('Payer') }}</label>
								<select class="form-control select2" name="payee_id">
								   <option value="">{{ _lang('Select One') }}</option>
								   {{ create_option("contacts","id","contact_name",isset($payee_id) ? $payee_id : "",array("company_id="=>company_id())) }}
								</select>   								
								</div>
							</div>
							
							<div class="col-md-2">
								<button type="submit" class="btn btn-primary btn-xs btn-block mt-26">{{ _lang('View Report') }}</button>
							</div>
						</form>

					</div>
				</div><!--End Report param-->
                
				@php $date_format = get_company_option('date_format','Y-m-d'); @endphp
			
				<div class="report-header">
				   <h4>{{ isset($account) ? _lang('Report By Payee Of').' '.$acc->account_title : _lang('Report By Payee') }}</h4>
				   <h5>{{ isset($date1) ? date($date_format, strtotime($date1)).' '._lang('to').' '.date($date_format, strtotime($date2)) : '-------------  '._lang('to').'  -------------' }}</h5>
				</div>

				<table class="table table-bordered report-table">
					<thead>
						<th>{{ _lang('Date') }}</th>   
						<th>{{ _lang('Expense Type') }}</th>   
						<th>{{ _lang('Note') }}</th>   
						<th>{{ _lang('Account') }}</th>   
						<th>{{ _lang('Payee') }}</th>   
						<th class="text-right">{{ _lang('Amount') }}</th>         
					</thead>
					<tbody>
					 
					@if(isset($report_data))
					   @php $currency = currency($acc->account_currency); @endphp
					  
					   @foreach($report_data as $report) 
						<tr>
						   <td>{{ date($date_format, strtotime($report->trans_date)) }}</td>
						   <td>{{ $report->c_type }}</td>
						   <td>{{ $report->note }}</td>
						   <td>{{ $report->account }}</td>
						   <td>{{ $report->payee }}</td>
						   <td class="text-right">{{ decimalPlace($report->amount, $currency) }}</td>
						</tr>
					    @endforeach
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
	
})(jQuery);	
</script>
@endsection


