@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<span class="d-none panel-title">{{ _lang('Update Email Template') }}</span>

			<div class="card-body">
				<form method="post" class="validate" autocomplete="off" action="{{action('CompanyEmailTemplateController@update', $id)}}" enctype="multipart/form-data">
					{{ csrf_field()}}
					<input name="_method" type="hidden" value="PATCH">				
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Related To') }}</label>						
								<select class="form-control auto-select" data-selected="{{ $companyemailtemplate->related_to }}" name="related_to" id="email_template_related_to" required>
									<option value="invoice">{{ _lang('Invoice') }}</option>
									<option value="quotation">{{ _lang('Quotation') }}</option>
								</select>
							</div>
						</div>
				
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Name') }}</label>						
								<input type="text" class="form-control" name="name" value="{{ $companyemailtemplate->name }}" required>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Subject') }}</label>						
								<input type="text" class="form-control" name="subject" value="{{ $companyemailtemplate->subject }}" required>
							</div>
						</div>
						
						<div class="col-md-12">
							<pre id="invoice-paremeter" class="border border-info p-2  {{ $companyemailtemplate->related_to == 'invoice' ? '' : 'd-none' }}">{customer_name},{invoice_no},{invoice_date},{due_date},{payment_status},{grand_total},{amount_due},{total_paid},{invoice_link}</pre>
							<pre id="quotation-paremeter" class="border border-info p-2 {{ $companyemailtemplate->related_to == 'quotation' ? '' : 'd-none' }}">{customer_name},{quotation_no},{quotation_date},{grand_total},{quotation_link}</pre>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Body') }}</label>						
								<textarea class="form-control summernote" name="body">{{ $companyemailtemplate->body }}</textarea>
							</div>
						</div>
					
						<div class="col-md-12">
							<div class="form-group">
								<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection


