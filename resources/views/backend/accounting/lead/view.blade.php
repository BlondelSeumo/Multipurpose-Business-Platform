@extends('layouts.app')

@section('content')
 <style>
	#main_modal .modal-lg {
	    max-width: 900px;
	}

	#main_modal .modal-body {
	    overflow: visible !important;
	}
</style>
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<span class="panel-title d-none">{{ _lang('View Lead Details') }}</span>

			@php $date_format = get_company_option('date_format','Y-m-d'); @endphp	
			@php $time_format = get_company_option('time_format',24) == '24' ? 'H:i' : 'h:i A'; @endphp	
			@php $currency = currency() @endphp

			<div class="card-body">
				<ul class="nav nav-tabs" id="leads-tab">
				  <li class="nav-item">
				    <a class="nav-link active" data-toggle="tab" href="#lead_details">{{ _lang('Lead Details') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#quotations">{{ _lang('Quotation') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#files">{{ _lang('Files') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#notes">{{ _lang('Notes') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" data-toggle="tab" href="#activity_log">{{ _lang('Activity Log') }}</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link ajax-modal" href="{{ action('LeadController@edit', $lead->id) }}" data-title="{{ _lang('Update Lead') }}">{{ _lang('Edit') }}</a>
				  </li>
				</ul>

				<div class="tab-content mt-4">
					<div class="tab-pane active" id="lead_details">
						<table class="table table-bordered">
							<tr>
								<td>{{ _lang('Name') }}</td>
								<td>
									<span class="float-left pt-1">{{ $lead->id.'# -'.$lead->name }}</span> 
									<a href="" class="btn btn-info btn-xs float-right"><i class="fas fa-user-tie"></i> {{ _lang('Convert to Customer') }}</a>
								</td>
							</tr>
							<tr><td>{{ _lang('Company Name') }}</td><td>{{ $lead->company_name }}</td></tr>
							<tr><td>{{ _lang('Email') }}</td><td>{{ $lead->email }}</td></tr>
							<tr>
								<td>{{ _lang('Lead Status') }}</td>
								<td><span class='badge badge-primary' style="background:{{ $lead->lead_status->color }}">{{ $lead->lead_status->title }}</span></td>
							</tr>
							<tr><td>{{ _lang('Lead Source') }}</td><td>{{ $lead->lead_source->title }}</td></tr>
							<tr><td>{{ _lang('Assigned ') }}</td><td>{{ $lead->assigned_user->name }}</td></tr>
							<tr><td>{{ _lang('Contact Date') }}</td><td>{{ date($date_format, strtotime($lead->contact_date)) }}</td></tr>
							<tr><td>{{ _lang('Phone') }}</td><td>{{ $lead->phone }}</td></tr>
							<tr><td>{{ _lang('Website') }}</td><td>{{ $lead->website }}</td></tr>
							<tr><td>{{ _lang('Country') }}</td><td>{{ $lead->country }}</td></tr>
							<tr><td>{{ _lang('Currency') }}</td><td>{{ $lead->currency }}</td></tr>
							<tr><td>{{ _lang('Vat Id') }}</td><td>{{ $lead->vat_id }}</td></tr>
							<tr><td>{{ _lang('Reg No') }}</td><td>{{ $lead->reg_no }}</td></tr>
							<tr><td>{{ _lang('City') }}</td><td>{{ $lead->city }}</td></tr>
							<tr><td>{{ _lang('State') }}</td><td>{{ $lead->state }}</td></tr>
							<tr><td>{{ _lang('Zip') }}</td><td>{{ $lead->zip }}</td></tr>
							<tr><td>{{ _lang('Address') }}</td><td>{{ $lead->address }}</td></tr>
							<tr><td>{{ _lang('Created') }}</td><td>{{ date($date_format, strtotime($lead->created_at)) }}</td></tr>
						</table>
					</div>

					<div class="tab-pane" id="quotations">
						<a href="{{ route('quotations.create') }}?related_to=leads&lead_id={{ $lead->id }}" class="btn btn-info btn-xs mb-4"><i class="ti-plus"></i> {{ _lang('Create New') }}</a>
						<table id="quotation-table" class="table table-bordered data-table">
							<thead>
							  <tr>
								<th>{{ _lang('Quotation Number') }}</th>
								<th>{{ _lang('Quotation To') }}</th>
								<th>{{ _lang('Quotation Date') }}</th>
								<th class="text-right">{{ _lang('Grand Total') }}</th>
								<th class="text-center">{{ _lang('Action') }}</th>
							  </tr>
							</thead>
							<tbody>
				               @foreach($quotations as $quotation)
				               @php $lead_currency = currency($quotation->lead->currency); @endphp
				 				<tr>
									<td>{{ $quotation->quotation_number }}</td>
									<td>{{ $quotation->lead->name }}</td>
									<td>{{ date($date_format, strtotime($quotation->quotation_date)) }}</td>
									<td class="text-right">
										@if($lead_currency != $currency)
											<span>{{ decimalPlace($quotation->grand_total, $currency) }}</span><br>
											<span><b>{{ decimalPlace($quotation->converted_total, $currency) }}</b></span>
										@else
											<span>{{ decimalPlace($quotation->grand_total, $currency) }}</span>
										@endif
									</td>
									<td class="text-center">
										<div class="dropdown">
											<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}
											<i class="fas fa-angle-down"></i></button>
											<div class="dropdown-menu">
												<a class="dropdown-item" href="{{ action('QuotationController@edit', $quotation->id) }}"><i class="fas fa-edit"></i> {{ _lang('Edit') }}</a></li>
												<a class="dropdown-item" href="{{ action('QuotationController@show', $quotation->id) }}"><i class="fas fa-eye"></i> {{ _lang('View') }}</a></li>

												<form action="{{ action('QuotationController@destroy', $quotation['id']) }}" method="post">							
													{{ csrf_field() }}
													<input name="_method" type="hidden" value="DELETE">
													<button class="button-link btn-remove" type="submit"><i class="fas fa-recycle"></i> {{ _lang('Delete') }}</button>
												</form>	
											</div>
										</div>
									</td>
								</tr>
				               @endforeach
							</tbody>
						</table>
					</div>

					<div class="tab-pane" id="files">
						<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('leads.upload_file') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="row">
								<input type="hidden" name="related_id" value="{{ $lead->id }}" required>

								<div class="col-md-12">
								    <div class="form-group">
									    <label class="control-label">{{ _lang('Upload File') }}</label>
									    <input type="file" class="form-control dropify" name="file" required>
								    </div>
								</div>
								
								<div class="col-md-12">
								    <div class="form-group">
									    <button type="submit" class="btn btn-primary">{{ _lang('UPLOAD NOW') }}</button>
								    </div>
								</div>
							</div>			
					    </form>
					    <table id="files_table" class="table table-bordered">
							<thead>
							    <tr>
								    <th>{{ _lang('Upload Date') }}</th>
								    <th>{{ _lang('Uploaded') }}</th>
									<th>{{ _lang('File') }}</th>
									<th class="text-center">{{ _lang('Remove') }}</th>
							    </tr>
							</thead>
							<tbody>
							    @foreach($projectfiles as $projectfile)
							    <tr data-id="row_{{ $projectfile->id }}">
							    	<td class="created_at">{{ date("$date_format $time_format", strtotime($projectfile->created_at)) }}</td>
									<td class='user_id'><a href="{{action('StaffController@show', $projectfile->user->id)}}" data-title="{{ _lang('View Staf Information') }}"class="ajax-modal-2">{{ $projectfile->user->name }}</a></td>
									<td class='file'><a href="{{ url('leads/download_file/'.$projectfile->file) }}">{{ $projectfile->file }}</a></td>
									<td class="remove text-center"><a class="ajax-get-remove" href="{{ url('leads/delete_file/'.$projectfile->id) }}">{{ _lang('Remove') }}</a></td>
							    </tr>
							    @endforeach
							    <tr data-id="files_id">
							    	<td class="created_at"></td>
							    	<td class="user_id"></td>
									<td class='file'></td>
									<td class="remove text-center"></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="tab-pane" id="notes">
						<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('leads.create_note') }}">
							{{ csrf_field() }}
							<div class="row">
								<input type="hidden" name="related_id" value="{{ $lead->id }}" required>
							
								<div class="col-md-12">
								    <div class="form-group">
									    <label class="control-label">{{ _lang('Note') }}</label>						
									    <textarea class="form-control" name="note" required>{{ old('note') }}</textarea>
								    </div>
								</div>
								
								<div class="col-md-12">
								    <div class="form-group">
									    <button type="submit" class="btn btn-primary">{{ _lang('ADD NOTE') }}</button>
								    </div>
								</div>
							</div>			
					    </form>

					    <div class="crm-scroll">
						    <table id="notes_table" class="table">
								<tbody>
								    @foreach($notes as $note)
								    <tr data-id="row_{{ $note->id }}">
										<td class='created'>
											<small>
												<a href="{{action('StaffController@show', $note->user->id)}}" data-title="{{ _lang('View Staf Information') }}"class="ajax-modal-2">{{ $note->user->name }}</a>
												({{ date("$date_format $time_format", strtotime($note->created_at)) }})<br>
												{{ $note->note }}
											</small>
										</td>	
										<td class="action wp-100">
											<a href="{{ url('leads/delete_note/'.$note->id) }}" class="note-remove ajax-get-remove"><i class="far fa-trash-alt text-danger"></i></a>
										</td>
								    </tr>
								    @endforeach

								    <tr data-id="notes_id">
										<td class='created'></td>	
										<td class="action wp-100"></td>
								    </tr>
								</tbody>
							</table>
						</div>
					</div>	

					<div class="tab-pane" id="activity_log">

					     <div class="crm-scroll">
						    <table id="activity_log_table" class="table table-bordered">
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

				</div>
			</div>
	    </div>
	</div>
</div>
@endsection

@section('js-script')
<script>
	(function($) {
    "use strict";

	$('#leads-tab a').on('shown.bs.tab', function(event){
		var tab = $(event.target).attr("href");
		if(tab == '#activity_log'){
	        $.ajax({
	        	url: "{{ url('leads/get_logs_data/'.$lead->id) }}",
	        	beforeSend: function(){
	        		$("#preloader").fadeIn();
	        	},success: function(data){
	        		$("#preloader").fadeOut();
	        		var json = JSON.parse(data);
					var rows = '';

	        		$.each(json, function(index, element) {
					    rows += `<tr id="row_${element.id}">
										<td class='created'>
											<small>
												${element.created_at}<br>
												${element.created_by.name} - ${element.activity}
											</small>
										</td>	
								    </tr>`;
								    
					});

					$("#activity_log_table tbody").html(rows);

	        	}
	        });
		}
	});

})(jQuery);	
</script>
@endsection	
