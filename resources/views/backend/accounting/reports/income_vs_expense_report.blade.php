@extends('layouts.app')

@section('content')
<style>
  .btn{margin-bottom: 2px !important;}
</style>
<div class="row">
	<div class="col-12">
		<div class="card">
			
			<span class="d-none panel-title">{{ _lang('Income VS Expense Report') }}</span>

			<div class="card-body">
			
				<div class="report-params">
					<form class="validate" method="post" action="{{ url('reports/income_vs_expense/view') }}">
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

						    <div class="col-md-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('From') }}</label>					
									<input type="text" class="form-control datepicker" name="date1" id="date1" value="{{ isset($date1) ? $date1 : old('date1') }}" readOnly="true" required>   
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('To') }}</label>
									<input type="text" class="form-control datepicker" name="date2" id="date2" value="{{ isset($date2) ? $date2 : old('date2') }}" readOnly="true" required>						
								</div>
							</div>
							
							<div class="col-md-3">
								<button type="submit" class="btn btn-primary btn-xs btn-block mt-26">{{ _lang('View Report') }}</button>
							</div>
						</form>

					</div>
				</div><!--End Report param-->
                
				@php $date_format = get_company_option('date_format','Y-m-d');	 @endphp 
				
				<div class="report-header">
				   <h4>{{ isset($account) ? _lang('Income VS Expense Report Of').' '.$acc->account_title : _lang('Income VS Expense Report') }}</h4>
				   <h5>{{ isset($date1) ? date($date_format, strtotime($date1)).' '._lang('to').' '.date($date_format, strtotime($date2)) : '-------------  '._lang('to').'  -------------' }}</h5>
				</div>

				<table class="table table-bordered report-table">
					<thead>
						<th>{{ _lang('Income Date') }}</th>
						<th>{{ _lang('Income Type') }}</th>
						<th class="text-right">{{ _lang('Amount') }}</th>
						<th>{{ _lang('Expense Date') }}</th>
						<th>{{ _lang('Expense Type') }}</th>
						<th class="text-right">{{ _lang('Amount') }}</th>
					</thead>
					<tbody>
					 
					@if(isset($report_data))
						@php 
							$currency = currency($acc->account_currency);
							$income_total = 0;
							$expense_total = 0;				
						@endphp
			
					   @foreach($report_data as $report) 
						 <tr>
							 <td>{{ date($date_format, strtotime($report->income_date)) }}</td>
							 <td>{{ $report->income_type }}</td>
							 <td class="text-right">{{ decimalPlace($report->income_amount, $currency) }}</td>
							 <td>{{ date($date_format, strtotime($report->expense_date)) }}</td>
							 <td>{{ $report->expense_type }}</td>
							 <td class="text-right">{{ decimalPlace($report->expense_amount, $currency) }}</td>
						   </tr>
						   
							@php
							  $income_total += $report->income_amount;
							  $expense_total += $report->expense_amount;
							@endphp
					    @endforeach
						 <tr>
							 <td></td>
							 <td>{{ _lang('Total Income') }}</td>
							 <td class="text-right">{{ $currency." ".decimalPlace($income_total) }}</td>
							 <td></td>
							 <td>{{ _lang('Total Expense') }}</td>
							 <td class="text-right">{{ $currency." ".decimalPlace($expense_total) }}</td>
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
})(jQuery);	
</script>
@endsection


