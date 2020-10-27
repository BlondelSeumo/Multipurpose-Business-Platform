<li>
	<a href="javascript: void(0);"><i class="ti-user"></i><span>{{ _lang('User Management') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ url('users/type/user') }}">{{ _lang('User List') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ url('users/create') }}">{{ _lang('Add New') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="ti-gift"></i><span>{{ _lang('Packages') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ url('packages') }}">{{ _lang('Packages') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ url('packages/create') }}">{{ _lang('Add New') }}</a></li>		
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="ti-credit-card"></i><span>{{ _lang('Payments') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ url('offline_payment/create') }}">{{ _lang('Offline Payment') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ url('members/payment_history') }}">{{ _lang('Payment History') }}</a></li>		
	</ul>
</li>


<li>
	<a href="javascript: void(0);"><i class="ti-world"></i><span>{{ _lang('Languages') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ url('languages') }}">{{ _lang('All Language') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ url('languages/create') }}">{{ _lang('Add New') }}</a></li>		
	</ul>
</li>

<li @if(Request::is('admin/email_subscribers')) class="mm-active" @endif>
    <a href="{{ url('admin/email_subscribers') }}"><i class="ti-email"></i><span>{{ _lang('Email Subscribers') }}</span></a>
</li>

<li>
	<a href="javascript: void(0);"><i class="ti-ruler-pencil"></i><span>{{ _lang('Website Settings') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ url('features') }}">{{ _lang('Software Features') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ url('faqs') }}">{{ _lang('Knowledge Base') }}</a></li>	
		<li class="nav-item"><a class="nav-link" href="{{ url('administration/theme_option') }}">{{ _lang('Theme Options') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="ti-settings"></i><span>{{ _lang('Administration') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ url('administration/general_settings') }}">{{ _lang('General Settings') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ url('email_templates') }}">{{ _lang('Email Template') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ url('administration/currency_rates') }}">{{ _lang('Exchange Rates') }}</a></li>	
		<li class="nav-item"><a class="nav-link" href="{{ url('administration/backup_database') }}">{{ _lang('Database Backup') }}</a></li>	
	</ul>
</li>