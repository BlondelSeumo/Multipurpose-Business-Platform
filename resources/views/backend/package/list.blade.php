@extends('layouts.app')

@section('content')

  <span class="panel-title d-none">{{ _lang('Package List') }}</span>
   
  @php $currency = currency(); @endphp
  <div class="row">
	  <div class="col-md-12 text-center"> 
		 <button class="btn btn-primary btn-xs" id="btn-monthly">{{ _lang('Monthly') }}</button>
	     <button class="btn btn-outline-info btn-xs" id="btn-yearly">{{ _lang('Yearly') }}</button>
	  </div>
  </div>
  
  <div class="row mt-2">
  @foreach($packages as $package)
	  <div class="col-lg-4 monthly-package">
			<div class="card">
				<div class="pb-4">
					<div class="pricing-list text-center">
					    <div class="prc-head">
							<h4>{{ $package->package_name }}</h4>
							<h3 class="amount d-inline-block mt-2">{{ decimalPlace($package->cost_per_month, $currency) }}</h3>
							<small class="font-12 text-muted">/{{ _lang('month') }}</small>
                        </div>
					   
						<ul class="text-left p-3">
							<li {{ unserialize($package->staff_limit)['monthly'] != 'No' ? 'class=yes-feature' : 'class=no-feature' }}>{{ unserialize($package->staff_limit)['monthly'].' '._lang('Staff Accounts') }}</li>
							<li {{ unserialize($package->contacts_limit)['monthly'] != 'No' ? 'class=yes-feature' : 'class=no-feature' }}>{{ unserialize($package->contacts_limit)['monthly'].' '._lang('Contacts') }}</li>
							<li {{ unserialize($package->invoice_limit)['monthly'] != 'No' ? 'class=yes-feature' : 'class=no-feature' }}>{{ unserialize($package->invoice_limit)['monthly'].' '._lang('Invoice') }}</li>
							<li {{ unserialize($package->quotation_limit)['monthly'] != 'No' ? 'class=yes-feature' : 'class=no-feature' }}>{{ unserialize($package->quotation_limit)['monthly'].' '._lang('Quotation') }}</li>
							<li {{ unserialize($package->project_management_module)['monthly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('Project Management') }}</li>
							<li {{ unserialize($package->recurring_transaction)['monthly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('Recurring Transaction') }}</li>
							<li {{ unserialize($package->inventory_module)['monthly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('Inventory Module') }}</li>
							<li {{ unserialize($package->live_chat)['monthly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('Live Chat') }}</li>
							<li {{ unserialize($package->file_manager)['monthly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('File Manager') }}</li>
							<li {{ unserialize($package->online_payment)['monthly'] == 'Yes'? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('Online Payment') }}</li>
						</ul>
						
						<form action="{{ action('PackageController@destroy', $package['id']) }}" method="post">
							<a href="{{ action('PackageController@edit', $package['id']) }}" class="btn btn-outline-dark btn-round">{{ _lang('Edit') }}</a>
							<a href="{{ action('PackageController@show', $package['id']) }}" data-title="{{ _lang('View Package') }}" class="btn btn-outline-primary btn-round ajax-modal">{{ _lang('View') }}</a>
							{{ csrf_field() }}
							<input name="_method" type="hidden" value="DELETE">
							<button class="btn btn-outline-danger btn-round btn-remove" type="submit">{{ _lang('Delete') }}</button>
						</form>							
					</div><!--end pricingTable-->
				</div><!--end card-body-->
			</div> <!--end card-->                                   
		</div>
		
		<!-- Yearly package -->
		<div class="col-lg-4 yearly-package">
			<div class="card">
				<div class="pb-4">
					<div class="pricing-list text-center">
					    <div class="prc-head">
							<h4>{{ $package->package_name }}</h4>
							<h3 class="amount d-inline-block mt-2">{{ decimalPlace($package->cost_per_year, $currency) }}</h3>
							<small class="font-12 text-muted">/{{ _lang('year') }}</small>
						</div>
					   
						<ul class="text-left p-3">
							<li {{ unserialize($package->staff_limit)['yearly'] != 'No' ? 'class=yes-feature' : 'class=no-feature' }}>{{ unserialize($package->staff_limit)['yearly'].' '._lang('Staff Accounts') }}</li>
							<li {{ unserialize($package->contacts_limit)['yearly'] != 'No' ? 'class=yes-feature' : 'class=no-feature' }}>{{ unserialize($package->contacts_limit)['yearly'].' '._lang('Contacts') }}</li>
							<li {{ unserialize($package->invoice_limit)['yearly'] != 'No' ? 'class=yes-feature' : 'class=no-feature' }}>{{ unserialize($package->invoice_limit)['yearly'].' '._lang('Invoice') }}</li>
							<li {{ unserialize($package->quotation_limit)['yearly'] != 'No' ? 'class=yes-feature' : 'class=no-feature' }}>{{ unserialize($package->quotation_limit)['yearly'].' '._lang('Quotation') }}</li>
							<li {{ unserialize($package->project_management_module)['yearly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('Project Management') }}</li>
							<li {{ unserialize($package->recurring_transaction)['yearly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('Recurring Transaction') }}</li>
							<li {{ unserialize($package->inventory_module)['yearly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('Inventory Module') }}</li>
							<li {{ unserialize($package->live_chat)['yearly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('Live Chat') }}</li>
							<li {{ unserialize($package->file_manager)['yearly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('File Manager') }}</li>
							<li {{ unserialize($package->online_payment)['yearly'] == 'Yes' ? 'class=yes-feature' : 'class=no-feature' }}>{{ _lang('Online Payment') }}</li>
						</ul>
						
						<form action="{{ action('PackageController@destroy', $package['id']) }}" method="post">
							<a href="{{ action('PackageController@edit', $package['id']) }}" class="btn btn-outline-dark btn-round">{{ _lang('Edit') }}</a>
							<a href="{{ action('PackageController@show', $package['id']) }}" data-title="{{ _lang('View Package') }}" class="btn btn-outline-primary btn-round ajax-modal">{{ _lang('View') }}</a>
							{{ csrf_field() }}
							<input name="_method" type="hidden" value="DELETE">
							<button class="btn btn-outline-danger btn-round btn-remove" type="submit">{{ _lang('Delete') }}</button>
						</form>							
					</div><!--end pricingTable-->
				</div><!--end card-body-->
			</div> <!--end card-->                                   
		</div>			
	@endforeach	
</div>

@endsection

@section('js-script')

<script>
$(document).on('click','#btn-monthly',function(){
	$(".monthly-package").fadeIn(800);
	$(".yearly-package").css('display','none');
	$(this).removeClass('btn-outline-info').addClass('btn-primary');
	$('#btn-yearly').removeClass('btn-primary').addClass('btn-outline-info');
});

$(document).on('click','#btn-yearly',function(){
	$(".yearly-package").fadeIn(800);
	$(".monthly-package").css('display','none');
	$(this).removeClass('btn-outline-info').addClass('btn-primary');
	$('#btn-monthly').removeClass('btn-primary').addClass('btn-outline-info');
});


</script>
@endsection


