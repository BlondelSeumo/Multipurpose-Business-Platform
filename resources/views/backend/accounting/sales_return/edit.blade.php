@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
	<div class="card">
	<span class="d-none panel-title">{{ _lang('Update Sales Return') }}</span>

	<div class="card-body">
		<form method="post" class="validate" autocomplete="off" action="{{ action('SalesReturnController@update', $id) }}" enctype="multipart/form-data">
			<div class="row">  
				{{ csrf_field()}}
				<input name="_method" type="hidden" value="PATCH">	
			
				<div class="col-md-4">
					<div class="form-group">
					<label class="control-label">{{ _lang('Return Date') }}</label>						
					<input type="text" class="form-control datepicker" name="return_date" value="{{ $sales->return_date }}" readOnly="true" required>
					</div>
				</div>
				
				<div class="col-md-4">
				  <div class="form-group">
					<a href="{{ route('contacts.create') }}" data-reload="false" data-title="{{ _lang('Add Client') }}" class="ajax-modal select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
					<label class="control-label">{{ _lang('Customer') }}</label>						
					<select class="form-control select2-ajax" data-value="id" data-display="contact_name" data-table="contacts" data-where="1" name="customer_id" id="customer_id" required>
						 <option value="">{{ _lang('Select One') }}</option>
						 {{ create_option("contacts","id","contact_name",$sales->customer_id,array("company_id="=>company_id())) }}
					</select>	
				  </div>
				</div>

				
				<div class="col-md-4">
					<div class="form-group">
					<label class="control-label">{{ _lang('Attachemnt') }}</label>						
					<input type="file" class="form-control trickycode-file" data-value="{{ $sales->attachemnt }}" name="attachemnt">
					</div>
				</div>

				<div class="col-md-12">
					<div class="form-group select-product-container">
					<a href="{{ route('products.create') }}" data-reload="false" data-title="{{ _lang('Add Product') }}" class="ajax-modal select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
					<label class="control-label">{{ _lang('Select Product') }}</label>						
					<select class="form-control select2-ajax" data-value="id" data-display="item_name" data-table="items" data-where="2" name="product" id="product">
						<option value="">{{ _lang('Select Product') }}</option>
					</select>
					</div>
				</div>


				@php $currency = currency(); @endphp

				<!--Order table -->
				<div class="col-md-12">
					<div class="table-responsive">
						<table id="order-table" class="table table-bordered">
							<thead>
								<tr>
									<th>{{ _lang('Name') }}</th>
									<th class="text-center wp-100">{{ _lang('Quantity') }}</th>
									<th class="text-right">{{ _lang('Unit Cost').' '.$currency }}</th>
									<th class="text-right wp-100">{{ _lang('Discount').' '.$currency }}</th>
									<th class="text-right">{{ _lang('Tax method') }}</th>
									<th class="text-right">{{ _lang('Tax').' '.$currency }}</th>
									<th class="text-right">{{ _lang('Sub Total').' '.$currency }}</th>
									<th class="text-center">{{ _lang('Action') }}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($sales->sales_return_items as $item)
									<tr id="product-{{ $item->product_id }}">
											<td>
												<b>{{ $item->item->item_name }}</b><br>
												<span class="description">{{ $item->description }}</span>
											</td>
											<td class="text-center quantity">{{ $item->quantity }}</td>
											<td class="text-right unit-cost">{{ $item->unit_cost }}</td>
											<td class="text-right discount">{{ $item->discount }}</td>
											<td class="text-right tax-method">{{ strtoupper($item->tax_method) }}</td>
											<td class="text-right tax">{{ $item->tax_amount }}</td>
											<td class="text-right sub-total">{{ $item->sub_total }}</td>
											<td class="text-center">
												<button type="button" class="btn btn-success btn-xs edit-product"><i class="fas fa-edit"></i></button>
												<button type="button" class="btn btn-danger btn-xs remove-product"><i class='fa fa-trash'></i></button>
											</td>
											<input type="hidden" name="return_item_id[]" value="{{ $item->id }}">
											<input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
											<input type="hidden" name="product_description[]" class="input-description" value="{{ $item->description }}">
											<input type="hidden" name="quantity[]" class="input-quantity" value="{{ $item->quantity }}">
											<input type="hidden" name="unit_cost[]" class="input-unit-cost" value="{{ $item->unit_cost }}">
											<input type="hidden" name="discount[]" class="input-discount" value="{{ $item->discount }}">
											<input type="hidden" name="tax_method[]" class="input-tax-method" value="{{ $item->tax_method }}">
											<input type="hidden" name="tax_amount[]" class="input-tax" value="{{ $item->tax_amount }}">
											<input type="hidden" name="tax_id[]" class="input-tax-id" value="{{ $item->tax_id }}">
											<input type="hidden" name="unit_tax[]" class="input-unit-tax" value="{{ $item->tax_amount/$item->quantity }}">
											<input type="hidden" name="sub_total[]" class="input-sub-total" value="{{ $item->sub_total }}">
									</tr>
								@endforeach
							</tbody>
							<tfoot class="tfoot active">
								<tr>
									<th>{{ _lang('Total') }}</th>
									<th class="text-center" id="total-qty">0</th>
									<th></th>
									<th class="text-right" id="total-discount">0.00</th>
									<th></th>
									<th class="text-right" id="total-tax">0.00</th>
									<th class="text-right" id="total">0.00</th>
									<th class="text-center"></th>
									<input type="hidden" name="product_total" id="product_total" value="0">
								</tr>
							</tfoot>
						</table>
						
						<table class="table table-striped">
						   <thead class="thead-light">
							  <tr>
								 <th>
									{{ _lang('Converted Amount') }} ({{ _lang('Client Currency') }} - <span class="client_currency">{{ base_currency() }}</span>)
									&emsp;<span id="converted_amount">{{ $currency }} 0.00</span>
								 </th>
							  </tr>
						   </thead>
						</table>
						
					</div>
				</div>
				<!--End Order table -->

				<div class="col-md-4 clear">
					<div class="form-group">
					<label class="control-label">{{ _lang('Order Tax')." ".$currency }}</label>						
					<select class="form-control select2" name="return_tax_id">
							<option value="">{{ _lang('No Tax') }}</option>
							@foreach(App\Tax::where("company_id",company_id())->get() as $tax)
								<option value="{{ $tax->id }}"  {{ $sales->tax_id == $tax->id ? 'selected' : ''  }}>{{ $tax->tax_name }} - {{ $tax->type =='percent' ? $tax->rate.' %' : $tax->rate }}</option>
							@endforeach
					</select>
					</div>
				</div>


				<div class="col-md-12">
					<div class="form-group">
					<label class="control-label">{{ _lang('Note') }}</label>						
					<textarea class="form-control" name="note">{{ $sales->note }}</textarea>
					</div>
				</div>

					
				<div class="col-md-12">
				  <div class="form-group">
						<button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
				  </div>
				</div>
			</div>
		</form>
	</div>
  </div>
 </div>
</div>
@endsection

@section('js-script')
<script>
var client_id = "{{ $sales->client_id }}";
var client_currency = "{{ $sales->customer->currency }}";
</script>

<script src="{{ asset('public/backend/assets/js/sales_return/edit.js') }}"></script>

<script>
(function($) {
    "use strict";
    
	update_summary();

	//Click Edit product
	$(document).on('click', '.edit-product', function() {
	    var tr = $(this).parent().parent();
	    current_row = tr;

	    //Get current value
	    var quantity = parseFloat($(tr).find(".quantity").html());
	    var c_unit_cost = parseFloat($(tr).find(".unit-cost").html());
	    var c_sub_total = parseFloat($(tr).find(".sub-total").html());
	    var c_discount = parseFloat($(tr).find(".discount").html());
	    var c_description = $(tr).children("td:first").find(".description").html().trim();
	    //var c_tax_amount = parseFloat($(tr).find(".tax").html());
	    var c_tax_method = $(tr).find(".input-tax-method").val();
	    var c_tax_id = $(tr).find(".input-tax-id").val();

	    var form = `<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">{{ _lang('TAX Method') }}</label>						
							<select class="form-control float-field" id="modal-tax_method">
	                            <option value="">{{ _lang('NONE') }}</option>
	                            <option value="inclusive">{{ _lang('INCLUSIVE') }}</option>
	                            <option value="exclusive">{{ _lang('EXCLUSIVE') }}</option>
							</select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">{{ _lang('Unit Price') }}</label>						
							<input type="number" class="form-control" value="${ c_tax_method == 'exclusive' ? c_unit_cost : c_sub_total }" id="modal-unit_cost">
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">{{ _lang('Quantity') }}</label>						
							<input type="number" class="form-control" value="${quantity}" id="modal-quantity">
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">{{ _lang('Discount').' '.currency() }}</label>						
							<input type="text" class="form-control float-field" value="${c_discount}" id="modal-discount">
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">{{ _lang('Tax') }}</label>						
							<select class="form-control" id="modal-tax_id">
	                            <option value="">{{ _lang('No Tax') }}</option>
								@foreach(App\Tax::where("company_id",company_id())->get() as $tax)
									 <option value="{{ $tax->id }}" data-tax-type="{{ $tax->type }}" data-tax-rate="{{ $tax->rate }}">{{ $tax->tax_name }} - {{ $tax->type =='percent' ? $tax->rate.' %' : $tax->rate }}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">{{ _lang('Description') }}</label>						
							<textarea class="form-control" id="modal-description">${c_description}</textarea>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<button type="button" id="update-product" class="btn btn-primary">{{ _lang('Save') }}</button>
						</div>
					</div>`;

	    $("#main_modal .modal-title").html("{{ _lang('Update Product') }}");
	    $("#main_modal .modal-body").html(form);
	    $("#modal-tax_method").val(c_tax_method);
	    $("#modal-tax_id").val(c_tax_id);
	    $("#main_modal").modal("show");

	    $(document).on('change','#modal-tax_method',function(){
	        if($(this).val() == 'inclusive'){
	            $("#modal-unit_cost").val(c_sub_total);
	        }else if($(this).val() == 'exclusive'){
				$("#modal-unit_cost").val(c_unit_cost);
	        }
	    });


	});

})(jQuery);



</script>
@endsection



