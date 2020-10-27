@extends('theme.default.layouts.website')

@section('header')
<div class="page-hero bg_cover" style="background-image: url({{ get_option('sub_banner_image') != '' ? asset('public/uploads/media/'.get_option('sub_banner_image')) : theme_asset('assets/images/header-bg.jpg') }})">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-8 col-lg-10">
				<div class="header-content text-center">
					<h3 class="header-title">{{ _lang('Pricing') }}</h3>
				</div> <!-- header content -->
			</div>
		</div> <!-- row -->
	</div> <!-- container -->
</div> <!-- header content -->
@endsection

@section('content')

<!--====== PRICING PART START ======-->

<section id="pricing" class="pricing-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-title text-center pb-10">
                    <h4 class="title">{{ _lang('Our Pricing') }}</h4>
                    <p class="text">{{ _lang('Stop wasting time and money designing and managing a website that does not get results. Happiness guaranteed!') }}</p>
                </div> <!-- section title -->
            </div>
        </div> <!-- row -->

        <div class="row mt-4">
            <div class="col-md-12 text-center"> 
                <button class="btn btn-primary btn-xs" id="btn-monthly">{{ _lang('Monthly Plan') }}</button>
                <button class="btn btn-outline-info btn-xs" id="btn-yearly">{{ _lang('Yearly Plan') }}</button>
            </div>
        </div>


        <div class="row justify-content-center">
            @php $currency = currency(get_option('currency','USD')); @endphp

            @foreach(\App\Package::all() as $package)
                <div class="col-lg-4 col-md-7 col-sm-9 monthly-package">
                    <div class="single-pricing {{ $package->is_featured == 1 ? 'pro' : '' }} mt-40">
                        
                        @if($package->is_featured )
                            <div class="pricing-baloon">
                                <img src="{{ asset('public/theme/default/assets/images/baloon.svg') }}" alt="baloon">
                            </div>
                        @endif

                        <div class="pricing-header {{ $package->is_featured == 1 ? '' : 'text-center' }}">
                            <h5 class="sub-title">{{ $package->package_name }}</h5>
                            <span class="price">{{ g_decimal_place($package->cost_per_month, $currency) }}</span>
                            <p class="year">{{ _lang('Per Month') }}</p>
                        </div>
                        <div class="pricing-list">
                            <ul>
                                <li><i class='lni {{ unserialize($package->staff_limit)['monthly'] != 'No' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _dlang(unserialize($package->staff_limit)['monthly']).' '._lang('Staff Accounts') }}</li>

                                <li><i class='lni {{ unserialize($package->contacts_limit)['monthly'] != 'No' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _dlang(unserialize($package->contacts_limit)['monthly']).' '._lang('Contacts') }}</li>

                                <li><i class='lni {{ unserialize($package->invoice_limit)['monthly'] != 'No' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _dlang(unserialize($package->invoice_limit)['monthly']).' '._lang('Invoice') }}</li>

                                <li><i class='lni {{ unserialize($package->quotation_limit)['monthly'] != 'No' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _dlang(unserialize($package->quotation_limit)['monthly']).' '._lang('Quotation') }}</li>

                                <li><i class='lni {{ unserialize($package->project_management_module)['monthly'] == 'Yes' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('Project Management') }}</li>
								
								<li><i class='lni {{ unserialize($package->recurring_transaction)['monthly'] == 'Yes' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('Recurring Transaction') }}</li>
								
                                <li><i class='lni {{ unserialize($package->inventory_module)['monthly'] == 'Yes' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('Inventory Module') }}</li>

                                <li><i class='lni {{ unserialize($package->live_chat)['monthly'] == 'Yes' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('Live Chat') }}</li>

                                <li><i class='lni {{ unserialize($package->file_manager)['monthly'] == 'Yes' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('File Manager') }}</li>
                                
                                <li><i class='lni {{ unserialize($package->online_payment)['monthly'] == 'Yes'? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('Online Payment') }}</li>
                            </ul>
                        </div>
                        <div class="pricing-btn text-center">
                            <a class="main-btn" href="{{ url('sign_up?package_type=monthly&package='.$package->id) }}">{{ _lang('GET STARTED') }}</a>
                        </div>
                    </div> <!-- single pricing -->
                </div>



                <div class="col-lg-4 col-md-7 col-sm-9 yearly-package">
                    <div class="single-pricing {{ $package->is_featured == 1 ? 'pro' : '' }} mt-40">
                        
                        @if($package->is_featured )
                            <div class="pricing-baloon">
                                <img src="{{ asset('public/theme/default/assets/images/baloon.svg') }}" alt="baloon">
                            </div>
                        @endif

                        <div class="pricing-header {{ $package->is_featured == 1 ? '' : 'text-center' }}">
                            <h5 class="sub-title">{{ $package->package_name }}</h5>
                            <span class="price">{{ g_decimal_place($package->cost_per_year, $currency) }}</span>
                            <p class="year">{{ _lang('Per Year') }}</p>
                        </div>
                        <div class="pricing-list">
                            <ul>
                                <li><i class='lni {{ unserialize($package->staff_limit)['yearly'] != 'No' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _dlang(unserialize($package->staff_limit)['yearly']).' '._lang('Staff Accounts') }}</li>

                                <li><i class='lni {{ unserialize($package->contacts_limit)['yearly'] != 'No' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _dlang(unserialize($package->contacts_limit)['yearly']).' '._lang('Contacts') }}</li>

                                <li><i class='lni {{ unserialize($package->invoice_limit)['yearly'] != 'No' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _dlang(unserialize($package->invoice_limit)['yearly']).' '._lang('Invoice') }}</li>

                                <li><i class='lni {{ unserialize($package->quotation_limit)['yearly'] != 'No' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _dlang(unserialize($package->quotation_limit)['yearly']).' '._lang('Quotation') }}</li>
								
								<li><i class='lni {{ unserialize($package->project_management_module)['yearly'] == 'Yes' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('Project Management') }}</li>

                                <li><i class='lni {{ unserialize($package->recurring_transaction)['yearly'] == 'Yes' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('Recurring Transaction') }}</li>
                                
								<li><i class='lni {{ unserialize($package->inventory_module)['yearly'] == 'Yes' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('Inventory Module') }}</li>
								
                                <li><i class='lni {{ unserialize($package->live_chat)['yearly'] == 'Yes' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('Live Chat') }}</li>

                                <li><i class='lni {{ unserialize($package->file_manager)['yearly'] == 'Yes' ? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('File Manager') }}</li>
                                
                                <li><i class='lni {{ unserialize($package->online_payment)['yearly'] == 'Yes'? 'lni-checkmark-circle' : 'lni-cross-circle' }}'></i>{{ _lang('Online Payment') }}</li>
                            </ul>
                        </div>
                        <div class="pricing-btn text-center">
                            <a class="main-btn" href="{{ url('sign_up?package_type=yearly&package='.$package->id) }}">{{ _lang('GET STARTED') }}</a>
                        </div>
                    </div> <!-- single pricing -->
                </div>

            @endforeach
            
        </div> <!-- row -->
    </div> <!-- conteiner -->
</section>

<!--====== Pricing PART ENDS ======-->

@endsection