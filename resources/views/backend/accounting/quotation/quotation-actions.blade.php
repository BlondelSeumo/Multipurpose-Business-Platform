<input type="text" class="form-control mb-2" id="quotation_link_2" value="{{ url('client/view_quotation/'.md5($quotation->id)) }}">
<div>
	<a class="btn btn-dark btn-xs" href="javascript:void(0);" id="copy_quotation_link_2"><i class="far fa-copy"></i> {{ _lang('Copy Quotation Link') }}</a>
	<a class="btn btn-secondary btn-xs ajax-modal" data-title="{{ _lang('Send Email') }}" href="{{ url('quotations/create_email/'.$quotation->id) }}"><i class="fas fa-envelope-open-text"></i> {{ _lang('Send Email') }}</a>
	@if($quotation->related_to == 'contacts')
	<a class="btn btn-success btn-xs" href="{{ url('quotations/convert_invoice/'.$quotation->id) }}"><i class="fas fa-exchange-alt"></i> {{ _lang('Convert to Invoice') }}</a>
	@endif
	<a class="btn btn-primary btn-xs print" href="#" data-print="quotation-view"><i class="fas fa-print"></i> {{ _lang('Print') }}</a>
	<a class="btn btn-danger btn-xs " href="{{ url('quotations/download_pdf/'.encrypt($quotation->id)) }}"><i class="fas fa-print"></i> {{ _lang('Export PDF') }}</a>
	<a class="btn btn-warning btn-xs" href="{{ action('QuotationController@edit', $quotation->id) }}"><i class="fas fa-edit"></i> {{ _lang('Edit') }}</a>
</div>
	