@extends('theme.default.layouts.website')

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

<!--====== PRICING PART ENDS ======-->

 <!--====== CALL TO ACTION PART START ======-->

<section id="call-to-action" class="call-to-action">
    <div class="call-action-image">
        <img src="{{ asset('public/theme/default/assets/images/call-to-action.png') }}" alt="call-to-action">
    </div>
    
    <div class="container-fluid">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <div class="call-action-content text-center">
                    <h2 class="call-title">{{ _lang('Curious to Learn More? Stay Tuned') }}</h2>
                    <p class="text">{{ _lang('Stop wasting time and money designing and managing a website that does not get results. Happiness guaranteed!') }}</p>
                    <form action="{{ url('emaiL_subscribed') }}" method="post">
                        {{ csrf_field() }}
                        <div class="call-newsletter">
                            <i class="lni lni-envelope"></i>
                            <input type="email" name="email" placeholder="{{ _lang('yourmail@email.com') }}" required>
                            <button type="submit">{{ _lang('SUBSCRIBE') }}</button>
                        </div>
                    </form>
                </div> <!-- slider-content -->
            </div>
        </div> <!-- row -->
    </div> <!-- container -->
</section>

<!--====== CALL TO ACTION PART ENDS ======-->

<!--====== CONTACT PART START ======-->

<section id="contact" class="contact-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-title text-center pb-10">
                    <h4 class="title">{{ _lang('Get In touch') }}</h4>
                    <p class="text">{{ _lang('Stop wasting time and money designing and managing a website that does not get results. Happiness guaranteed!') }}</p>
                </div> <!-- section title -->
            </div>
        </div> <!-- row -->
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="alert alert-success d-none" id="contact-message"></div>

                <div class="contact-form">
                    <form id="contact-form" action="{{ url('contact/send_message') }}" method="post" data-toggle="validator">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="single-form form-group">
                                    <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ _lang('Your Name') }}" data-error="Name is required." required="required">
                                    <div class="help-block with-errors"></div>
                                </div> <!-- single form -->
                            </div>
                            <div class="col-md-6">
                                <div class="single-form form-group">
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ _lang('Your Email') }}" data-error="Valid email is required." required="required">
                                    <div class="help-block with-errors"></div>
                                </div> <!-- single form -->
                            </div>
                            <div class="col-md-12">
                                <div class="single-form form-group">
                                    <input type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ _lang('Subject') }}" data-error="Subject is required." required="required">
                                    <div class="help-block with-errors"></div>
                                </div> <!-- single form -->
                            </div>

                            <div class="col-md-12">
                                <div class="single-form form-group">
                                    <textarea placeholder="{{ _lang('Your Mesaage') }}" name="message" data-error="Please, leave us a message." required="required">{{ old('message') }}</textarea>
                                    <div class="help-block with-errors"></div>
                                </div> <!-- single form -->
                            </div>
                            <p class="form-message"></p>
                            <div class="col-md-12">
                                <div class="single-form form-group text-center">
                                    <button type="submit" class="main-btn">{{ _lang('send message') }}</button>
                                </div> <!-- single form -->
                            </div>
                        </div> <!-- row -->
                    </form>
                </div> <!-- row -->
            </div>
        </div> <!-- row -->
    </div> <!-- conteiner -->
</section>

<!--====== CONTACT PART ENDS ======-->

@endsection