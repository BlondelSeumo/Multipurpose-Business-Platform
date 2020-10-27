<li><a href="{{ url('client/projects') }}"><i class="ti-briefcase"></i><span>{{ _lang('Projects') }}</span></a></li>
<li>
	<a href="javascript: void(0);"><i class="ti-file"></i><span>{{ _lang('Invoices') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ url('client/invoices') }}">{{ _lang('All Invoices') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ url('client/invoices/Unpaid') }}">{{ _lang('Unpaid Invoices') }}</a></li>	
		<li class="nav-item"><a class="nav-link" href="{{ url('client/invoices/Paid') }}">{{ _lang('Paid Invoices') }}</a></li>	
		<li class="nav-item"><a class="nav-link" href="{{ url('client/invoices/Partially_Paid') }}">{{ _lang('Partially Paid Invoices') }}</a></li>	
		<li class="nav-item"><a class="nav-link" href="{{ url('client/invoices/Canceled') }}">{{ _lang('Canceled Invoices') }}</a></li>	
	</ul>
</li>

<li><a href="{{ url('client/quotations') }}"><i class="ti-files"></i><span>{{ _lang('Quotation') }}</span></a></li>
<li><a href="{{ url('client/transactions') }}"><i class="ti-wallet"></i><span>{{ _lang('Transactions') }}</span></a></li>

@if(get_option('live_chat') == 'enabled')
	<li>
       <a href="{{ url('live_chat') }}"><i class="far fa-comment"></i><span>{{ _lang('Messenger') }}</span><span class="chat-notification {{ unread_message_count() > 0 ? 'show' : 'hidden' }}">{{ unread_message_count() }}</span></a>
	</li>
@endif