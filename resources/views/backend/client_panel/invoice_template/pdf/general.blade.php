<!DOCTYPE html>
<html lang="en">
<head>
<title>{{ get_option('site_title', 'ElitKit Invoice') }}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style type="text/css">
@php include public_path('backend/assets/css/bootstrap.min.css') @endphp
@php include public_path('backend/assets/css/styles.css') @endphp

    body { 
	   -webkit-print-color-adjust: exact; !important;
	   background: #FFF;
	   font-size: 14px;
	}
	.table th {
		background-color: #2a77d6 !important;
		color: #ffffff;
	}
	
	.base_color{
		background: #2a77d6 !important;
	}
	.invoice-box {
		margin: auto;
		padding: 15px 0px;
		min-height: auto;
	}
	.invoice-logo{
		width: 100px;
	}
	.invoice-col-6{
	  width: 50%;
	  float:left;
	  padding-right: 0px;
	  padding-left: 0px;
	}
	
</style> 
</head>

<body>

	@php $base_currency = get_company_field( $invoice->company_id, 'base_currency', 'USD' ); @endphp
	@php $date_format = get_company_field($invoice->company_id, 'date_format','Y-m-d'); @endphp	
	@php $currency = get_currency_symbol($base_currency); @endphp

	@if($invoice->related_to == 'contacts' && isset($invoice->client))
		@php $client_currency = $invoice->client->currency; @endphp
		@php $client = $invoice->client; @endphp
	@else 
		@php $client_currency = $invoice->project->client->currency; @endphp
		@php $client = $invoice->project->client; @endphp
	@endif
	
	<div class="invoice-box pdf" id="invoice-view">
		 <table cellpadding="0" cellspacing="0">
				<tbody>
					 <tr class="top">
						<td colspan="2">
							<table>
								<tbody>
									 <tr>
										<td>
											 <b>{{ _lang('Invoice') }} #:</b>  {{ $invoice->invoice_number }}<br>
											 <b>{{ _lang('Created') }}: </b>{{ date($date_format, strtotime( $invoice->invoice_date)) }}<br>
											 <b>{{ _lang('Due Date') }}: </b>{{ date($date_format, strtotime( $invoice->due_date)) }}							
											 <div class="invoice-status {{ strtolower($invoice->status) }}">{{ _dlang(str_replace('_',' ',$invoice->status)) }}</div>
										</td>
										<td class="invoice-logo">
											<img src="{{ get_company_logo($invoice->company_id) }}" class="wp-100">
										</td>
									 </tr>
								</tbody>
							</table>
						</td>
					 </tr>
					 <tr class="information">
						<td colspan="2">
							<div class="invoice-col-6 pt-3">
								<h5><b>{{ _lang('Invoice To') }}</b></h5>	
								{{ $client->contact_name }}<br>
								{{ $client->contact_email }}<br>
								{!! $client->company_name != '' ? clean($client->company_name).'<br>' : '' !!}
								{!! $client->address != '' ? clean($client->address).'<br>' : '' !!}
								{!! $client->vat_id != '' ? _lang('VAT ID').': '.clean($client->vat_id).'<br>' : '' !!}
								{!! $client->reg_no != '' ? _lang('REG NO').': '.clean($client->reg_no).'<br>' : '' !!}                        
							</div>
							<!--Company Address-->
							<div class="invoice-col-6 pt-3">
								<div class="d-inline-block float-md-right">
									<h5><b>{{ _lang('Company Details') }}</b></h5>
									{{ get_company_field($invoice->company_id,'company_name') }}<br>
									{{ get_company_field($invoice->company_id,'address') }}<br>
									{{ get_company_field($invoice->company_id,'email') }}<br>
									{!! get_company_field($invoice->company_id,'vat_id') != '' ? _lang('VAT ID').': '.clean(get_company_field($invoice->company_id,'vat_id')).'<br>' : '' !!}
									{!! get_company_field($invoice->company_id,'reg_no')!= '' ? _lang('REG NO').': '.clean(get_company_field($invoice->company_id,'reg_no')).'<br>' : '' !!}
									<!--Invoice Payment Information-->
									<h5>{{ _lang('Invoice Total') }}: {!! strip_tags(decimalPlace($invoice->grand_total, $currency)) !!}</h5>
									@if($client_currency != $base_currency)
									   <h5>{!! strip_tags(decimalPlace($invoice->converted_total, get_currency_symbol($client_currency))) !!}</h5>	
									@endif
								</div>
							</div>
							<div class="clearfix"></div>
						</td>
					 </tr>
				</tbody>
		 </table>
		 <!--End Invoice Information-->
		
		 <!--Invoice Product-->
		 <div>
			<table class="table">
				 <thead class="base_color">
					 <tr>
						 <th>{{ _lang('Name') }}</th>
						 <th class="text-center wp-100">{{ _lang('Quantity') }}</th>
						 <th class="text-right">{{ _lang('Unit Cost') }}</th>
						 <th class="text-right wp-100">{{ _lang('Discount') }}</th>
						 <th class="no-print">{{ _lang('Tax method') }}</th>
						 <th class="text-right">{{ _lang('Tax') }}</th>
						 <th class="text-right">{{ _lang('Sub Total') }}</th>
					 </tr>
				 </thead>
				 <tbody  id="invoice">
					 @foreach($invoice->invoice_items as $item)
						 <tr id="product-{{ $item->item_id }}">
							 <td>
								<b>{{ $item->item->item_name }}</b><br>{{ $item->description }}
							 </td>
							 <td class="text-center">{{ $item->quantity }}</td>
							 <td class="text-right">{!! strip_tags(decimalPlace($item->unit_cost, $currency)) !!}</td>
							 <td class="text-right">{!! strip_tags(decimalPlace($item->discount, $currency)) !!}</td>
							 <td class="no-print">{{ strtoupper($item->tax_method) }}</td>
							 <td class="text-right">{!! strip_tags(decimalPlace($item->tax_amount, $currency)) !!}</td>
							 <td class="text-right">{!! strip_tags(decimalPlace($item->sub_total, $currency)) !!}</td>
						 </tr>
					 @endforeach
				 </tbody>
			</table>
		 </div>
		 <!--End Invoice Product-->	
		 <!--Summary Table-->
		 <div class="invoice-summary-right">
			<table class="table table-bordered">
				 <tbody>
					<tr>
						 <td>{{ _lang('Tax') }}</td>
						 <td class="text-right">
							<span>{!! strip_tags(decimalPlace($invoice->tax_total, $currency)) !!}</span>
							@if($client_currency != $base_currency)
								<br><span>{!! strip_tags(decimalPlace(convert_currency($base_currency, $client_currency, $invoice->tax_total), get_currency_symbol($client_currency))) !!}</span>	
							@endif
						 </td>
					</tr>
					<tr>
						 <td><b>{{ _lang('Grand Total') }}</b></td>
						 <td class="text-right">
							 <b>{!! strip_tags(decimalPlace($invoice->grand_total, $currency)) !!}</b>
							 @if($client_currency != $base_currency)
								<br><b>{!! strip_tags(decimalPlace($invoice->converted_total, get_currency_symbol($client_currency))) !!}</b>
							 @endif
						 </td>
					</tr>
					<tr>
						 <td>{{ _lang('Total Paid') }}</td>
						 <td class="text-right">
							<span>{!! strip_tags(decimalPlace($invoice->paid, $currency)) !!}</span>
							@if($client_currency != $base_currency)
								<br><span>{!! strip_tags(decimalPlace(convert_currency($base_currency, $client_currency, $invoice->paid), get_currency_symbol($client_currency))) !!}</span>	
							@endif
						 </td>
					</tr>
					@if($invoice->status != 'Paid')
						<tr>
							 <td>{{ _lang('Amount Due') }}</td>
							 <td class="text-right">
								<span>{!! strip_tags(decimalPlace(($invoice->grand_total - $invoice->paid), $currency)) !!}</span>
								@if($client_currency != $base_currency)
								<br><span>{!! strip_tags(decimalPlace(convert_currency($base_currency, $client_currency, ($invoice->grand_total - $invoice->paid)), get_currency_symbol($client_currency))) !!}</span>	
								@endif
							 </td>
						</tr>
					@endif
				</tbody>
			</table>
		 </div>
		 <!--End Summary Table-->
		 <div class="clearfix"></div>
		 <!--Related Transaction-->
		 @if( ! $transactions->isEmpty() )

			<table class="table table-bordered mt-2">
				<thead class="base_color">
					<tr>
					   <th colspan="4" class="text-center">{{ _lang('Payment History') }}</td>
					</tr>
					<tr>
						<th>{{ _lang('Date') }}</th>
						<th>{{ _lang('Account') }}</th>
						<th class="text-right">{{ _lang('Amount') }}</th>
						<th class="text-right">{{ _lang('Base Amount') }}</th>
						<th>{{ _lang('Payment Method') }}</th>
					</tr>
				</thead>
				<tbody>
				    @foreach($transactions as $transaction)
						<tr id="transaction-{{ $transaction->id }}">
							<td>{{ date($date_format, strtotime($transaction->trans_date)) }}</td>
							<td>{{ $transaction->account->account_title }}</td>
							<td class="text-right">{!! strip_tags(decimalPlace($transaction->amount, get_currency_symbol($transaction->account->account_currency))) !!}</td>
							<td class="text-right">{!! strip_tags(decimalPlace($transaction->base_amount,$currency)) !!}</td>
							<td>{{ $transaction->payment_method->name }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		 @endif
		 <!--END Related Transaction-->	
		 
		 <!--Invoice Note-->
		 @if($invoice->note  != '')
			<div class="mt-4">
				<div class="invoice-note">{{ $invoice->note }}</div>
			</div> 
		 @endif
		 <!--End Invoice Note-->
		 
		 <!--Invoice Footer Footer-->
		 @if(get_company_field($invoice->company_id,'invoice_footer')  != '')
			<div class="mt-4">
				<div class="invoice-note">{!! xss_clean(get_company_field($invoice->company_id,'invoice_footer')) !!}</div>
			</div> 
		 @endif
		 <!--End Footer Text-->
		 
	</div>
</body>
</html>


