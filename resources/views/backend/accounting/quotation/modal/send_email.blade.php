<form action="{{ url('quotations/send_email') }}" class="validate" method="post">
	{{ csrf_field() }}
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Email Template') }}</label>						
		<select class="form-control select2" id="email_template">
		    <option value="">{{ _lang('Select Email Template') }}</option>
		    {{ create_option("company_email_template","id","name",old('email_template'),array("company_id=" => company_id(), " and related_to=" => "quotation")) }}
		</select>
	  </div>
	</div>
	
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Client Quotation Link') }} (<a href="javascript: void(0);" id="copy_quotation_link">{{ _lang('Click to Copy') }}</a>)</label>						
		<input type="text" class="form-control" id="quotation_link" value="{{ url('client/view_quotation/'.md5($quotation->id)) }}" readOnly="true">
	  </div>
	</div>
	
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Email Subject') }}</label>						
		<input type="text" class="form-control" id="email_subject" name="email_subject" value="{{ old('email_subject') }}" required>
	  </div>
	</div>
	<div class="col-md-12">
	  <div class="form-group">
		<label class="control-label">{{ _lang('Email Message') }}</label>						
		<textarea class="form-control summernote" id="email_message" name="email_message">{{ old('email_message') }}</textarea>
	  </div>
	</div>
	
	<input type="hidden" name="contact_email" value="{{ $client_email }}">
	<input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
	
	<div class="col-md-12">
	  <div class="form-group">
		<button type="submit" class="btn btn-primary">{{ _lang('Send Email') }}</button>
	  </div>
	</div>
</form>

<script>
(function($) {
    "use strict";
	
	$(document).on('change','#email_template',function(){
		if($(this).val() != ''){
			$.ajax({
				url: "{{ url('company_email_template/get_template') }}/" + $(this).val(),
				beforeSend: function(){
					$("#preloader").css("display","block");
				},success: function(data){
					$("#preloader").css("display","none");
					var json = JSON.parse(data);
					$("#email_subject").val(json['subject']);
					tinymce.activeEditor.setContent(json['body']);
				}
			});
		}
	});
	
})(jQuery);	
</script>