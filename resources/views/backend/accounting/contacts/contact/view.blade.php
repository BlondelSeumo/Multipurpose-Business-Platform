@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-md-3">
		<div class="card">
			<div class="d-none panel-title">{{ _lang('View Contact') }}</div>

			<div class="card-body p-3">
				<div class="row">
					<div class="col-lg-12 align-self-center">
						<div class="contact-profile text-center">
							<div class="contact-profile-image">
								<img src="{{ asset('public/uploads/contacts/'.$contact->contact_image) }}" alt="" class="thumb-contact rounded-circle">
							</div>
							<div class="contact-profile-detail">
								<h4 class="mt-2">{{ $contact->contact_name }}</h4>                                             
								<p class="mb-0">{{ $contact->group->name }}</p>
							</div>
						</div>                                                
					</div><!--end col-->
				</div><!--end row-->                                                                              
			</div><!--end card-body-->
			
			<div class="card-body p-3">
			  <ul class="nav flex-column nav-tabs settings-tab">
				  <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#general-info" aria-expanded="true"><i class="far fa-user"></i> {{ _lang('General') }}</a></li>
				  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#projects" aria-expanded="false"><i class="fas fa-briefcase"></i> {{ _lang('Projects') }}</a></li>
				  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#invoices" aria-expanded="false"><i class="fas fa-file-invoice-dollar"></i> {{ _lang('Invoices') }}</a></li>
				  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#quotations" aria-expanded="false"><i class="fas fa-file-invoice"></i> {{ _lang('Quotations') }}</a></li>
				  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#transactions" aria-expanded="false"><i class="fas fa-credit-card"></i> {{ _lang('Transactions') }}</a></li>
				  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#email" aria-expanded="false"><i class="far fa-envelope-open"></i> {{ _lang('Email') }}</a></li>
				  <li class="nav-item"><a class="nav-link" href="{{ action('ContactController@edit', $contact['id']) }}"><i class="far fa-edit"></i> {{ _lang('Edit') }}</a></li>
			  </ul>
			</div><!--end card-body-->
		</div><!--end card-->
	</div><!--end col-->

	@php 

	$currency = currency();
	$base_currency = base_currency();
    $date_format = get_company_option('date_format','Y-m-d');
    
    @endphp
	  
	<div class="col-md-9">	  
	  <div class="tab-content" id="crm-tab">
	
	      <div id="general-info" class="tab-pane active">
			  <div class="card">
				  <div class="card-body">

                    <div class="row">

						<div class="col-lg-6 mb-3">
                       		<div class="card">
								<div class="seo-fact sbg2">
									<div class="p-4">
										<div class="seofct-icon">
										    <i class="ti-briefcase"></i> 
											<span class="float-right">{{ _lang('Total Project') }}</span>
										</div>
										<h2 class="text-right">
											{{ $total_project }}
										</h2>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-6 mb-3">
                       		<div class="card">
								<div class="seo-fact sbg1">
									<div class="p-4">
										<div class="seofct-icon">
										    <i class="ti-file"></i> 
											<span class="float-right">{{ _lang('Invoice Value') }}</span>
										</div>
										<h2 class="text-right">
											{{ decimalPlace($invoice_value->grand_total, $currency) }}
										</h2>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-6 mb-3">
                       		<div class="card">
								<div class="seo-fact sbg2">
									<div class="p-4">
										<div class="seofct-icon">
										    <i class="ti-check-box"></i> 
											<span class="float-right">{{ _lang('Total Payment') }}</span>
										</div>
										<h2 class="text-right">
											{{ decimalPlace($invoice_value->paid, $currency) }}
										</h2>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-6 mb-3">
                       		<div class="card">
								<div class="seo-fact sbg3">
									<div class="p-4">
										<div class="seofct-icon">
										    <i class="ti-info-alt"></i> 
											<span class="float-right">{{ _lang('Total Due') }}</span>
										</div>
										<h2 class="text-right">
											{{ decimalPlace(($invoice_due_amount->grand_total - $invoice_due_amount->paid), $currency) }}
										</h2>
									</div>
								</div>
							</div>
						</div>


					</div>

					<table class="table table-striped">
						<thead>
						    <th colspan="2"><h5>{{ _lang('General Information') }}</h5></th>
						</thead>
						<tbody>
							<tr><td>{{ _lang('Profile Type') }}</td><td><b>{{ $contact->profile_type }}</b></td></tr>
							<tr><td>{{ _lang('Company Name') }}</td><td><b>{{ $contact->company_name }}</b></td></tr>
							<tr><td>{{ _lang('Contact Name') }}</td><td><b>{{ $contact->contact_name }}</b></td></tr>
							<tr><td>{{ _lang('Group') }}</td><td><b>{{ $contact->group->name }}</b></td></tr>
							<tr><td>{{ _lang('VAT ID') }}</td><td><b>{{ $contact->vat_id }}</b></td></tr>
							<tr><td>{{ _lang('Reg No') }}</td><td><b>{{ $contact->reg_no }}</b></td></tr>
							<tr><td>{{ _lang('Contact Email') }}</td><td><b>{{ $contact->contact_email }}</b></td></tr>
							<tr><td>{{ _lang('Contact Phone') }}</td><td><b>{{ $contact->contact_phone }}</b></td></tr>
							<tr><td>{{ _lang('Country') }}</td><td><b>{{ $contact->country }}</b></td></tr>
							<tr><td>{{ _lang('Currency') }}</td><td><b>{{ $contact->currency }} ({!! clean(get_currency_symbol( $contact->currency )) !!})</b></td></tr>
							<tr><td>{{ _lang('City') }}</td><td><b>{{ $contact->city }}</b></td></tr>
							<tr><td>{{ _lang('State') }}</td><td><b>{{ $contact->state }}</b></td></tr>
							<tr><td>{{ _lang('Zip') }}</td><td><b>{{ $contact->zip }}</b></td></tr>
							<tr><td>{{ _lang('Address') }}</td><td><b>{{ $contact->address }}</b></td></tr>
							<tr><td>{{ _lang('Facebook') }}</td><td><b>{{ $contact->facebook }}</b></td></tr>
							<tr><td>{{ _lang('Twitter') }}</td><td><b>{{ $contact->twitter }}</b></td></tr>
							<tr><td>{{ _lang('Linkedin') }}</td><td><b>{{ $contact->linkedin }}</b></td></tr>
							<tr><td>{{ _lang('Remarks') }}</td><td><b>{{ $contact->remarks }}</b></td></tr>
						</tbody>
					</table>
				  </div>
			  </div>
		  </div>

		  <div id="projects" class="tab-pane fade"> 
		  	<div class="card">
				<div class="card-body">
				  	<table class="table table-bordered data-table">
					      <thead>
						    <tr>
								<th>{{ _lang('Name') }}</th>	
								<th>{{ _lang('Start Date') }}</th>
								<th>{{ _lang('End Date') }}</th>
								<th>{{ _lang('Status') }}</th>
								<th>{{ _lang('Progress') }}</th>
								<th class="text-center">{{ _lang('Action') }}</th>
						    </tr>
						</thead>
						<tbody>
							@foreach($contact->projects as $project)
								<tr>
									<td><a href="{{ action('ProjectController@show', $project->id) }}">{{ $project->name }}</a></td>
									<td>{{ date($date_format,strtotime($project->start_date)) }}</td>
									<td>{{ date($date_format,strtotime($project->end_date)) }}</td>
									<td>{!! clean(project_status($project->status)) !!}</td>
									<td>
										<div class="progress">
										  <div class="progress-bar" role="progressbar" style="width: {{ $project->progress }}%;" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">{{ $project->progress }}%</div>
										</div>
									</td>
									<td>
										<form action="{{ action('ProjectController@destroy', $project['id']) }}" class="text-center" method="post">
											<a href="{{ action('ProjectController@show', $project['id']) }}" class="btn btn-primary btn-xs"><i class="ti-eye"></i></a>
											<a href="{{ action('ProjectController@edit', $project['id']) }}" data-title="'. _lang('Update Project') .'" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>
											{{ csrf_field() }}
											<input name="_method" type="hidden" value="DELETE">
											<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i></button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		  </div>

		  <div id="invoices" class="tab-pane fade"> 
			  <div class="card">
				    <div class="card-body">
					  <table class="table table-bordered data-table">
						<thead>
						  <tr>
							<th>{{ _lang('Invoice Number') }}</th>
							<th>{{ _lang('Due Date') }}</th>
							<th class="text-right">{{ _lang('Grand Total') }}</th>
							<th class="text-right">{{ _lang('Paid') }}</th>
							<th class="text-center">{{ _lang('Status') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						  </tr>
						</thead>
						<tbody>
						  
						  @foreach($invoices as $invoice)
						  <tr id="row_{{ $invoice->id }}">
							<td class='invoice_number'>{{ $invoice->invoice_number }}</td>
							<td class='due_date'>{{ date($date_format,strtotime($invoice->due_date)) }}</td>
							<td class='grand_total text-right'>{{ decimalPlace($invoice->grand_total, $currency) }}</td>
							<td class='paid text-right'>{{ decimalPlace($invoice->paid, $currency) }}</td>
							<td class='status text-center'>{!! strip_tags(invoice_status($invoice->status),'<span>') !!}</td>
							<td class="text-center">

								<div class="dropdown">
									<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}
									<i class="fa fa-angle-down"></i></button>
									<ul class="dropdown-menu">
										<a class="dropdown-item" href="{{ action('InvoiceController@edit', $invoice->id) }}"><i class="fas fa-edit"></i> {{ _lang('Edit') }}</a>
										<a class="dropdown-item" href="{{ action('InvoiceController@show', $invoice->id) }}" data-title="{{ _lang('View Invoice') }}" data-fullscreen="true"><i class="fas fa-eye"></i> {{ _lang('View') }}</a>
										<a class="dropdown-item ajax-modal" href="{{ url('invoices/create_payment/'.$invoice->id) }}" data-title="{{ _lang('Make Payment') }}"><i class="fas fa-credit-card"></i> {{ _lang('Make Payment') }}</a>
										<a class="dropdown-item ajax-modal" href="{{ url('invoices/view_payment/'.$invoice->id) }}" data-title="{{ _lang('View Payment') }}" data-fullscreen="true"><i class="fas fa-credit-card"></i> {{ _lang('View Payment') }}</a>
										
										<form action="{{action('InvoiceController@destroy', $invoice['id'])}}" method="post">									
											{{ csrf_field() }}
											<input name="_method" type="hidden" value="DELETE">
											<button class="button-link btn-remove" type="submit"><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>
										</form>
											
									</ul>
								</div>
							</td>
						  </tr>
						  @endforeach
						</tbody>
					  </table>
				    </div>
			    </div>
		  </div>
		  
		  <div id="quotations" class="tab-pane fade">
		      @php $currency = currency() @endphp
			  <div class="card">
				    <div class="card-body">
					  <table class="table table-bordered data-table">
						<thead>
						  <tr>
							<th>{{ _lang('Quotation Number') }}</th>
							<th>{{ _lang('Date') }}</th>
							<th class="text-right">{{ _lang('Grand Total') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						  </tr>
						</thead>
						<tbody>
						  
						  @foreach($quotations as $quotation)
						  <tr id="row_{{ $quotation->id }}">
							<td class='invoice_number'>{{ $quotation->quotation_number }}</td>
							<td class='due_date'>{{ date($date_format, strtotime($quotation->quotation_date)) }}</td>
							<td class='grand_total text-right'>{{ decimalPlace($quotation->grand_total, $currency) }}</td>
							<td class="text-center">

								<div class="dropdown">
									<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}
									<i class="fa fa-angle-down"></i></button>
									<ul class="dropdown-menu">
										<a class="dropdown-item" href="{{ action('QuotationController@edit', $quotation->id) }}"><i class="fas fa-edit"></i> {{ _lang('Edit') }}</a>
										<a class="dropdown-item" href="{{ action('QuotationController@show', $quotation->id) }}" data-title="{{ _lang('View Invoice') }}" data-fullscreen="true"><i class="fas fa-eye"></i> {{ _lang('View') }}</a>
										<a class="dropdown-item" href="{{ action('QuotationController@convert_invoice', $quotation->id) }}"><i class="fas fa-credit-card"></i> {{ _lang('Convert to Invoice') }}</a>
										
										<form action="{{action('QuotationController@destroy', $quotation->id)}}" method="post">									
											{{ csrf_field() }}
											<input name="_method" type="hidden" value="DELETE">
											<button class="button-link btn-remove" type="submit"><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>
										</form>
										
									</ul>
								</div>
							</td>
						  </tr>
						  @endforeach
						</tbody>
					  </table>
				    </div>
			    </div>
		  </div>
		  
		  <div id="transactions" class="tab-pane fade">
			<div class="card">
				<div class="card-body">	
					<table class="table table-bordered data-table">
						<thead>			
							<th>{{ _lang('Date') }}</th>
							<th>{{ _lang('Account') }}</th>
							<th>{{ _lang('Category') }}</th>
							<th class="text-right">{{ _lang('Amount') }}</th>
							<th>{{ _lang('Payment Method') }}</th>
						</thead>
						<tbody>
						   @foreach($transactions as $transaction)
							 <tr>
								<td>{{ date($date_format, strtotime($transaction->trans_date)) }}</td>
								<td>{{ isset($transaction->account) ? $transaction->account->account_title : '' }}</td>
								<td>{{ $transaction->income_type->name }}</td>
								<td class="text-right">{{ decimalPlace($transaction->amount, $currency) }}</td>
								<td>{{ isset($transaction->payment_method) ? $transaction->payment_method->name : '' }}</td>
							</tr>
						   @endforeach
						</tbody>
				   </table>
				</div>
			 </div>
		  </div>
		  
		  <div id="email" class="tab-pane fade">
		    <div class="card">
				<div class="card-body">	
					<form action="{{ url('contacts/send_email/'.$contact->id) }}" class="validate" method="post">
						{{ csrf_field() }}
						<div class="col-md-12">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Email Subject') }}</label>						
							<input type="text" class="form-control" name="email_subject" value="{{ old('email_subject') }}" required>
						  </div>
						</div>
						<div class="col-md-12">
						  <div class="form-group">
							<label class="control-label">{{ _lang('Email Message') }} *</label>						
							<textarea class="form-control summernote" name="email_message">{{ old('email_message') }}</textarea>
						  </div>
						</div>
						<div class="col-md-12">
						  <div class="form-group">
							<button type="submit" class="btn btn-primary">{{ _lang('Send Email') }}</button>
						  </div>
						</div>
					</form>
			     </div>
			  </div>
		  </div>
 
	  </div> <!--End TAB-->
	</div><!--End Col-->
</div><!--End Row-->
@endsection


@section('js-script')
<script>
(function($) {
    "use strict";
    
	$('.nav-tabs a').on('shown.bs.tab', function(event){
		var tab = $(event.target).attr("href");
		var url = "{{ url('contacts/'.$contact->id) }}";
	    history.pushState({}, null, url + "?tab=" + tab.substring(1));
	});

	@if(isset($_GET['tab']))
	   $('.nav-tabs a[href="#{{ $_GET['tab'] }}"]').tab('show')
	@endif

})(jQuery);
</script>
@endsection


