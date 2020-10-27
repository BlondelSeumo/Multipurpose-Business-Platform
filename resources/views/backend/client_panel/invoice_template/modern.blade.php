@extends('layouts.public')

@section('content')
<style type="text/css">
.classic-table{
	width:100%;
	color: #000;
}
.classic-table td{
	color: #000;
}

#invoice-item-table th{
	border: none;
}

#invoice-summary-table td{
	border: none !important;
}

#invoice-payment-history-table{
	margin-bottom: 50px;
}

#invoice-payment-history-table th{
	border: none !important;
}

#invoice-view{
   padding:15px;	
}

.invoice-note{
	margin-bottom: 50px;
}

.table th {
   background-color: #008ae2 !important;
   color: #FFF;
}

.table td {
   color: #2d2d2d;
}

.base_color{
	background-color: #008ae2 !important;
}
			
</style>  
</head>

<body>

<div class="row">
	<div class="col-md-12">
	    
		@include('backend.client_panel.invoice_template.invoice_actions')
		
		<div class="btn-group mb-1">
			<a class="btn btn-primary btn-round print" href="#" data-print="invoice-view"> {{ _lang('Print Invoice') }}</a>
			<a class="btn btn-danger btn-round" href="{{ url('invoices/download_pdf/'.encrypt($invoice->id)) }}" >{{ _lang('PDF Invoice') }}</a>
	    </div>
		
	<div class="card clearfix">
	
	
	@php $base_currency = get_company_field( $invoice->company_id, 'base_currency', 'USD' ); @endphp
	@php $date_format = get_company_field($invoice->company_id, 'date_format','Y-m-d'); @endphp	
	@php $currency = currency($base_currency); @endphp

	@if($invoice->related_to == 'contacts' && isset($invoice->client))
		@php $client_currency = $invoice->client->currency; @endphp
		@php $client = $invoice->client; @endphp
	@else 
		@php $client_currency = $invoice->project->client->currency; @endphp
		@php $client = $invoice->project->client; @endphp
	@endif
	
	<div class="card-body">
		<div id="invoice-view">
			<div> 
				<table class="classic-table">
					<tbody>
						<tr class="top">
							<td colspan="2">
								<table class="classic-table">
									<tbody>
										 <tr>
											<td class="wp-100">
												<img src="{{ get_company_logo($invoice->company_id) }}" class="wp-100">  
											</td>
											<td>	
												<div class="text-left">
													<b class="fs-22">{{ get_company_field($invoice->company_id,'company_name') }}</b><br>
													{{ get_company_field($invoice->company_id,'address') }}<br>
													{{ get_company_field($invoice->company_id,'email') }}<br>
													{!! get_company_field($invoice->company_id,'vat_id') != '' ? _lang('VAT ID').': '.clean(get_company_field($invoice->company_id,'vat_id')).'<br>' : '' !!}
													{!! get_company_field($invoice->company_id,'reg_no')!= '' ? _lang('REG NO').': '.clean(get_company_field($invoice->company_id,'reg_no')).'<br>' : '' !!}
												</div>
											</td>
											<td class="text-right">
												<img src="{{ asset('public/images/modern-invoice-bg.jpg') }}" class="wp-300 d-none d-md-inline">
											</td>
										 </tr>
									</tbody>
								</table>
							</td>
						</tr>
						 
						<tr class="information">
							<td colspan="2" class="pt-5">
								<div class="row">
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
											<h5><b>{{ _lang('Invoice Details') }}</b></h5>
											
											<b>{{ _lang('Invoice') }} #:</b> {{ $invoice->invoice_number }}<br>
											
											<b>{{ _lang('Invoice Date') }}:</b> {{ date($date_format, strtotime( $invoice->invoice_date)) }}<br>
																							
											<b>{{ _lang('Due Date') }}:</b> {{ date($date_format, strtotime( $invoice->due_date)) }}<br>							
											
											<b>{{ _lang('Payment Status') }}:</b> {{ _dlang(str_replace('_',' ',$invoice->status)) }}<br>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			 </div>
			 <!--End Invoice Information-->
			 
			 <!--Invoice Product-->
			 <div class="table-responsive">
				<table class="table table-bordered mt-2" id="invoice-item-table">
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
					 <tbody id="invoice">
						 @foreach($invoice->invoice_items as $item)
							 <tr id="product-{{ $item->item_id }}">
								 <td>
									<b>{{ $item->item->item_name }}</b><br>{{ $item->description }}
								 </td>
								 <td class="text-center">{{ $item->quantity }}</td>
								 <td class="text-right">{{ decimalPlace($item->unit_cost, $currency) }}</td>
								 <td class="text-right">{{ decimalPlace($item->discoun, $currency) }}</td>
								 <td class="no-print">{{ isset($item->item->product) ? strtoupper($item->item->product->tax_method) : strtoupper($item->item->service->tax_method)  }}</td>
								 <td class="text-right">{{ decimalPlace($item->tax_amount, $currency) }}</td>
								 <td class="text-right">{{ decimalPlace($item->sub_total, $currency) }}</td>
							 </tr>
						 @endforeach
					 </tbody>
				</table>
			 </div>
			 <!--End Invoice Product-->	
			 
			 <!--Summary Table-->
			 <div class="invoice-summary-right">
				<table class="table table-bordered" id="invoice-summary-table">
					 <tbody>
							<tr>
								 <td>{{ _lang('Tax') }}</td>
								 <td class="text-right">
									<span>{{ decimalPlace($invoice->tax_total, $currency) }}</span>
									@if($client_currency != $base_currency)
										<br><span>{{ decimalPlace(convert_currency($base_currency, $client_currency, $invoice->tax_total), currency($client_currency)) }}</span>	
									@endif
								 </td>
							</tr>
							<tr>
								 <td><b>{{ _lang('Grand Total') }}</b></td>
								 <td class="text-right">
									 <b>{{ decimalPlace($invoice->grand_total, $currency) }}</b>
									 @if($client_currency != $base_currency)
										<br><b>{{ decimalPlace($invoice->converted_total, currency($client_currency)) }}</b>
									 @endif
								 </td>
							</tr>
							<tr>
								 <td>{{ _lang('Total Paid') }}</td>
								 <td class="text-right">
									<span>{{ decimalPlace($invoice->paid, $currency) }}</span>
									@if($client_currency != $base_currency)
										<br><span>{{ decimalPlace(convert_currency($base_currency, $client_currency, $invoice->paid), currency($client_currency)) }}</span>	
									@endif
								 </td>
							</tr>
							@if($invoice->status != 'Paid')
								<tr>
									 <td>{{ _lang('Amount Due') }}</td>
									 <td class="text-right">
										<span>{{ decimalPlace(($invoice->grand_total - $invoice->paid), $currency) }}</span>
										@if($client_currency != $base_currency)
										<br><span>{{ decimalPlace(convert_currency($base_currency, $client_currency, ($invoice->grand_total - $invoice->paid)), currency($client_currency)) }}</span>	
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
				<div class="table-responsive">
					<table class="table table-bordered" id="invoice-payment-history-table">
						<thead class="base_color">
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
									<td>{{ $transaction->account->account_title.' - '.$transaction->account->account_currency }}</td>
									<td class="text-right">{{ decimalPlace($transaction->amount, currency($transaction->account->account_currency)) }}</td>
									<td class="text-right">{{ decimalPlace($transaction->base_amount, $currency) }}</td>
									<td>{{ $transaction->payment_method->name }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			 @endif
			 <!--END Related Transaction-->		
			 
			 <!--Invoice Note-->
			 @if($invoice->note  != '')
				<div class="">
					<div class="invoice-note">{{ $invoice->note }}</div>
				</div> 
			 @endif
			 <!--End Invoice Note-->
			 
			 <!--Invoice Footer Text-->
			 @if(get_company_field($invoice->company_id,'invoice_footer') != '')
				<div class="">
					<div class="invoice-note">{!! xss_clean(get_company_field($invoice->company_id,'invoice_footer')) !!}</div>
				</div> 
			 @endif
			 <!--End Invoice Note-->
		</div>
	</div>
  </div>
</div>
</div>
@endsection