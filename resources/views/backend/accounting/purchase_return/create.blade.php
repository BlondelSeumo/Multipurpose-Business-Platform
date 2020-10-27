@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
	<div class="card">
	<span class="d-none panel-title">{{ _lang('Create Purchase Return') }}</span>

	<div class="card-body">
		  <form method="post" class="validate" autocomplete="off" action="{{ url('purchase_returns') }}" enctype="multipart/form-data">
			{{ csrf_field() }}
			
			<div class="row">
				<div class="col-md-4">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Return Date') }}</label>						
					<input type="text" class="form-control datepicker" name="return_date" value="{{ old('return_date') }}" readOnly="true" required>
				  </div>
				</div>

				<div class="col-md-4">
				  <div class="form-group">
					<a href="{{ route('suppliers.create') }}" data-reload="false" data-title="{{ _lang('Add Supplier') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
					<label class="control-label">{{ _lang('Supplier') }}</label>						
					<select class="form-control select2-ajax" data-value="id" data-display="supplier_name" data-table="suppliers" data-where="1" name="supplier_id">
						 <option value="">{{ _lang('Select One') }}</option>
					</select>	
				  </div>
				</div>

				<div class="col-md-4">
				  <div class="form-group">
					<a href="{{ route('accounts.create') }}" data-reload="false" data-title="{{ _lang('Create Account') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
					<label class="control-label">{{ _lang('Credit Account') }}</label>						
					<select class="form-control select2-ajax" data-value="id" data-display="account_title" data-display2="account_currency" data-table="accounts" data-where="1" name="account_id" id="account_id" required>
						<option value="">{{ _lang('Select One') }}</option>
					    {{ create_option("accounts","id",array("account_title","account_currency"),old('account_id'),array("company_id="=>company_id())) }}
					</select>	
				  </div>
				</div>

				<div class="col-md-4">
				  <div class="form-group">
					<a href="{{ route('chart_of_accounts.create') }}" data-reload="false" data-title="{{ _lang('Add Income/Expense Type') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
					<label class="control-label">{{ _lang('Deposit Category') }}</label>						
					<select class="form-control select2-ajax" data-value="id" data-display="name" data-table="chart_of_accounts" data-where="3" name="chart_id" required>
						<option value="">{{ _lang('Select One') }}</option>
					</select>	
				  </div>
				</div>

				
				<div class="col-md-4">
					<div class="form-group">
					<a href="{{ route('payment_methods.create') }}" data-reload="false" data-title="{{ _lang('Add Payment Method') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
					<label class="control-label">{{ _lang('Deposit Payment Method') }}</label>						
					<select class="form-control select2-ajax" data-value="id" data-display="name" data-table="payment_methods" data-where="1" name="payment_method_id" required>
						<option value="">{{ _lang('Select One') }}</option>
					</select>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">{{ _lang('Attachemnt') }}</label>						
						<input type="file" class="form-control trickycode-file" name="attachemnt">
					</div>
				</div>


				<div class="col-md-8">
					<div class="form-group select-product-container">
					<a href="{{ route('products.create') }}" data-reload="false" data-title="{{ _lang('Add Product') }}" class="ajax-modal select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
					<label class="control-label">{{ _lang('Select Product') }}</label>						
					<select class="form-control select2-ajax" data-value="id" data-display="item_name" data-table="items" data-where="2" name="product" id="product">
						<option value="">{{ _lang('Select Product') }}</option>
					</select>
					</div>
				</div>

				@php $currency = currency(); @endphp

				<div class="col-md-4">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Order Tax')." ".$currency }}</label>						
					<select class="form-control select2" name="return_tax_id">
						 <option value="">{{ _lang('No Tax') }}</option>
						 @foreach(App\Tax::where("company_id",company_id())->get() as $tax)
							  <option value="{{ $tax->id }}">{{ $tax->tax_name }} - {{ $tax->type =='percent' ? $tax->rate.' %' : $tax->rate }}</option>
						 @endforeach
					</select>
				  </div>
				</div>

				
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
					</div>
				</div>

				<!--End Order table -->


				<div class="col-md-12">
				  <div class="form-group">
					<label class="control-label">{{ _lang('Note') }}</label>						
					<textarea class="form-control" name="note">{{ old('note') }}</textarea>
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
<script src="{{ asset('public/backend/assets/js/purchase_return.js') }}"></script>
<script>
(function($) {
    "use strict";
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


