<!DOCTYPE html>
<html lang="en">
<head>
<title>{{ get_option('site_title', 'ElitKit Purchase Order') }}</title>
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

.classic-table{
	width:100%;
	color: #000;
}
.classic-table td{
	color: #000;
	vertical-align: top;
}

#invoice-item-table th, #invoice-item-table td{
	border: 1px solid #000;
}

#invoice-summary-table td{
	border: 1px solid #000 !important;
}

#invoice-payment-history-table{
	margin-bottom: 50px;
}

#invoice-payment-history-table th, #invoice-payment-history-table td{
	border: 1px solid #000 !important;
}

#invoice-view{
   padding:15px 0px;	
}

.invoice-note{
	margin-bottom: 50px;
}

.table th {
   background-color: whitesmoke !important;
   color: #000;
}

.table td {
   color: #2d2d2d;
}

.base_color{
	background-color: whitesmoke !important;
}

.invoice-col-6{
	padding-left: 0px;
	padding-right: 0px;
}
	
</style>  
</head>

<body>

@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	

<div id="invoice-view" class="pdf">
	<table class="classic-table">
		<tbody>
			 <tr class="top">
				<td colspan="2">
					 <table class="classic-table">
						<tbody>
							 <tr>
								<td>
									<h3><b>{{ get_company_option('company_name') }}</b></h3>
									{{ get_company_option('address') }}<br>
									{{ get_company_option('email') }}<br>
									{!! get_company_option('vat_id') != '' ? _lang('VAT ID').': '.clean(get_company_option('vat_id')).'<br>' : '' !!}
									{!! get_company_option('reg_no')!= '' ? _lang('REG NO').': '.clean(get_company_option('reg_no')).'<br>' : '' !!}
								</td>
								<td class="wp-100">
									<img src="{{ get_company_logo() }}" class="wp-100">
								</td>
							 </tr>
						</tbody>
					 </table>
				</td>
			 </tr>
			 
			 <tr class="information">
				<td colspan="2" class="pt-5">				
					<div class="invoice-col-6 pb-2">
						 <h5><b>{{ _lang('Supplier Details') }}</b></h5>
						 @if(isset($purchase->supplier))	
							 <b>{{ _lang('Name') }}</b> : {{ $purchase->supplier->supplier_name }}<br>
							 <b>{{ _lang('Email') }}</b> : {{ $purchase->supplier->email }}<br>
							 <b>{{ _lang('Phone') }}</b> : {{ $purchase->supplier->phone }}<br>
							 <b>{{ _lang('VAT Number') }}</b> : {{ $purchase->supplier->vat_number == '' ? _lang('N/A') : $purchase->supplier->vat_number }}<br>
						 @endif                        
					</div>
						
					<!--Company Address-->
					<div class="invoice-col-6 pb-2">
						<div class="d-inline-block float-md-right">
							
							<h5><b>{{ _lang('Purchase Order') }}</b></h5>
							<b>{{ _lang('Order ID') }} #:</b> {{ $purchase->id }}<br>
							<b>{{ _lang('Order Date') }}:</b> {{ date($date_format, strtotime($purchase->order_date)) }}<br>
							
							<b>{{ _lang('Order Status') }}:</b>
								
								@if($purchase->order_status == 1)
									<span class="badge badge-info mt-1">{{ _lang('Ordered') }}</span><br>
								@elseif($purchase->order_status == 2)
									<span class="badge badge-danger mt-1">{{ _lang('Pending') }}</span><br>
								@elseif($purchase->order_status == 3)
									<span class="badge badge-success mt-1">{{ _lang('Received') }}</span><br>
								@elseif($purchase->order_status == 4)
									<span class="badge badge-danger mt-1">{{ _lang('Canceled') }}</span><br>
								@endif
															
								<b>{{ _lang('Payment') }}:</b>
									
							    @if($purchase->payment_status == 0) 
									<span class="badge badge-danger mt-1">{{ _lang('Due') }}</span><br>
								@else 
									<span class="badge badge-success mt-1">{{ _lang('Paid') }}</span><br>
								@endif								
							</div>
						</div>
					</div>			
				</td>
			</tr>
		</tbody>
	</table>
	<!--End Invoice Information-->

	<div class="clearfix"></div>

	@php $currency = currency(); @endphp
	
	<!--Invoice Product-->
	<table class="table table-bordered" id="invoice-item-table">
		<thead>
			<tr>
				<th>{{ _lang('Name') }}</th>
				<th class="text-center wp-100">{{ _lang('Quantity') }}</th>
				<th class="text-right">{{ _lang('Unit Cost') }}</th>
				<th class="text-right wp-100">{{ _lang('Discount')}}</th>
				<th class="text-right">{{ _lang('Tax method') }}</th>
				<th class="text-right">{{ _lang('Tax') }}</th>
				<th class="text-right">{{ _lang('Line Total') }}</th>
			</tr>
		</thead>

		<tbody>
			@foreach($purchase->purchase_items as $item)
				<tr id="product-{{ $item->product_id }}">
					<td>{{ $item->item->item_name }}</td>
					<td class="text-center quantity">{{ $item->quantity }}</td>
					<td class="text-right unit-cost">{{ decimalPlace($item->unit_cost, $currency) }}</td>
					<td class="text-right discount">{{ decimalPlace($item->discount, $currency) }}</td>
					<td class="text-right tax-method">{{ strtoupper($item->item->product->tax_method) }}</td>
					<td class="text-right tax">{{ decimalPlace($item->tax_amount, $currency) }}</td>
					<td class="text-right sub-total">{{ decimalPlace($item->sub_total, $currency) }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	<!--End Invoice Product-->	
	 
	 
	<!--Summary Table-->
	<div class="invoice-summary-right">
		<table class="table table-bordered" id="invoice-summary-table">
			 <tbody>
					<tr>
						 <td>{{ _lang('Sub Total') }}</td>
						  <td class="text-right">
							<span>{{ decimalPlace($purchase->product_total, $currency) }}</span>
						 </td>
					</tr>
					<tr>
						 <td>{{ _lang('Tax') }}</td>
						  <td class="text-right">
							<span>{{ decimalPlace($purchase->order_tax, $currency) }}</span>
						 </td>
					</tr>
					<tr>
						 <td>{{ _lang('Shipping Cost') }}</td>
						  <td class="text-right">
							<span>{{ decimalPlace($purchase->shipping_cost, $currency) }}</span>
						 </td>
					</tr>
					<tr>
						 <td>{{ _lang('Discount') }}</td>
						  <td class="text-right">
							<span>{{ decimalPlace($purchase->order_discount, $currency) }}</span>
						 </td>
					</tr>
					<tr>
						 <td><b>{{ _lang('Grand Total') }}</b></td>
						 <td class="text-right">
							 <b>{{ decimalPlace($purchase->grand_total, $currency) }}</b>
						 </td>
					</tr>
					<tr>
						 <td>{{ _lang('Total Paid') }}</td>
						 <td class="text-right">
							<span>{{ decimalPlace($purchase->paid, $currency) }}</span>
						 </td>
					</tr>
					@if($purchase->payment_status == 0)
						<tr>
							 <td>{{ _lang('Amount Due') }}</td>
							 <td class="text-right">
								<span>{{ decimalPlace(($purchase->grand_total - $purchase->paid), $currency) }}</span>
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
		<table class="table table-bordered" id="invoice-payment-history-table">
			<thead class="base_color">
				<tr>
				   <td colspan="5" class="text-center"><b>{{ _lang('Payment History') }}</b></td>
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
						<td>{{ $transaction->account->account_title.' - '.$transaction->account->account_currency }}</td>
						<td class="text-right">{{ decimalPlace($transaction->amount, currency($transaction->account->account_currency)) }}</td>
						<td class="text-right">{{ decimalPlace($transaction->base_amount, $currency) }}</td>
						<td>{{ $transaction->payment_method->name }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@endif
	<!--END Related Transaction-->

	<!--Invoice Note-->
	@if($purchase->note  != '')
		<div class="invoice-note border-top pt-4">{{ $purchase->note }}</div> 
	@endif
	<!--End Invoice Note-->
</div>
</body>
</html>
