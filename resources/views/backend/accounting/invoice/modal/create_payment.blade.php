<form method="post" class="ajax-submit" autocomplete="off" action="{{ url('invoices/store_payment') }}" enctype="multipart/form-data" novalidate>
	{{ csrf_field() }}

	<div class="col-12">
		<div class="row">
			
			<div class="col-md-6">
			  <div class="form-group">
				<a href="{{ route('accounts.create') }}" data-reload="false" data-title="{{ _lang('Create Account') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
				<label class="control-label">{{ _lang('Credit Account') }}</label>						
				<select class="form-control select2-ajax" data-value="id" data-display="account_title" data-display2="account_currency" data-table="accounts" data-where="1" name="account_id" id="account_id" required>
				   <option value="">{{ _lang('Select One') }}</option>
				   {{ create_option("accounts","id",array("account_title","account_currency"),old('account_id'),array("company_id="=>company_id())) }}
				</select>
			  </div>
			</div>

			<div class="col-md-6">
			  <div class="form-group">
				<a href="{{ route('chart_of_accounts.create') }}" data-reload="false" data-title="{{ _lang('Add Income/Expense Type') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
				<label class="control-label">{{ _lang('Income Type') }}</label>						
				<select class="form-control select2-ajax" data-value="id" data-display="name" data-table="chart_of_accounts" data-where="3" name="chart_id" required>
				   <option value="">{{ _lang('Select One') }}</option>
				   {{ create_option("chart_of_accounts","id","name",old('chart_id'),array("type="=>"income","AND company_id="=>company_id())) }}
				</select>
			  </div>
			</div>

			<div class="col-md-6">
			  <div class="form-group">
				<label class="control-label">{{ _lang('Pending Amount') }} (<b><span class="account_currency">{{ currency() }}</span></b>)</label>						
				<input type="text" class="form-control float-field" value="{{ ($invoice->grand_total - $invoice->paid) }}" id="pending_amount" readOnly="true">
			  </div>
			</div>

			<div class="col-md-6">
			  <div class="form-group">
				<label class="control-label">{{ _lang('Amount')." ".currency() }}</label>						
				<input type="text" class="form-control float-field" name="amount" value="{{ old('amount') }}" required>
			  </div>
			</div>

			<div class="col-md-6">
			  <div class="form-group">
				<a href="{{ route('payment_methods.create') }}" data-reload="false" data-title="{{ _lang('Add Payment Method') }}" class="ajax-modal-2 select2-add"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
				<label class="control-label">{{ _lang('Payment Method') }}</label>						
				<select class="form-control select2-ajax" data-value="id" data-display="name" data-table="payment_methods" data-where="1" name="payment_method_id" required>
				   <option value="">{{ _lang('Select One') }}</option>
				   {{ create_option("payment_methods","id","name",old('payment_method_id'),array("company_id="=>company_id())) }}
				</select>
			  </div>
			</div>

			<div class="col-md-6">
			  <div class="form-group">
				<label class="control-label">{{ _lang('Reference') }}</label>						
				<input type="text" class="form-control" name="reference" value="{{ old('reference') }}">
			  </div>
			</div>

			<div class="col-md-12">
				<div class="form-group">
				<label class="control-label">{{ _lang('Attachment') }}</label>						
				<input type="file" class="form-control dropify" name="attachment">
				</div>
			</div>

			<div class="col-md-12">
			  <div class="form-group">
				<label class="control-label">{{ _lang('Note') }}</label>						
				<textarea class="form-control" name="note">{{ old('note') }}</textarea>
			  </div>
			</div>

			<input type="hidden" name="invoice_id" value="{{ $id }}">
			<input type="hidden" name="client_id" value="{{ $invoice->client_id }}">

			<div class="col-md-12">
			  <div class="form-group">
				<button type="submit" class="btn btn-primary">{{ _lang('Make Payment') }}</button>
			  </div>
			</div>
		</div>
	</div>
</form>

<script>
(function($) {
    "use strict";	
	var from_currency = "{{ base_currency() }}";

	$(document).on('change','#account_id', function(){
		var account_currency = $(this).find( "option:selected" ).text().split(" ").pop();
		var amount = $("#pending_amount").val();
		
		$.ajax({
			method: "GET",
			url: "{{ url('convert_currency') }}/" + from_currency + "/" + account_currency + "/" + amount,
	        beforeSend: function(){
				$("#preloader").css("display","block"); 
			},success: function(data){
				$("#preloader").css("display","none");
				var json = JSON.parse(data);
				$("#pending_amount").val(json['amount'].toFixed(2));
				$(".account_currency").html(json['currency2_symbol']);
				from_currency = account_currency;
			}		
		});
	});
	
})(jQuery);	
</script>