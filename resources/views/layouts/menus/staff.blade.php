@php $permissions = permission_list(); @endphp

@if( has_feature( 'contacts_limit' ) )
<li>
	<a href="javascript: void(0);"><i class="ti-id-badge"></i><span>{{ _lang('Customers') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('contacts.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('contacts') }}">{{ _lang('Contacts List') }}</a></li>
		@endif
		
		@if (in_array('contacts.create',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('contacts/create') }}">{{ _lang('Add New') }}</a></li>	
		@endif
		
		@if (in_array('contact_groups.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('contact_groups') }}">{{ _lang('Contact Group') }}</a></li>	
	    @endif
	</ul>
</li>
@endif

@if( has_feature('project_management_module') )
	@if (in_array('leads.index', $permissions))
	<li>
	    <a href="{{ route('leads.index') }}"><i class="fas fa-tty"></i><span>{{ _lang('Leads') }}</span></a>
	</li>
	@endif

	@if (in_array('projects.index', $permissions))
	<li>
		<a href="{{ route('projects.index') }}"><i class="ti-briefcase"></i><span>{{ _lang('Projects') }}</span></a>
	</li>
	@endif

	@if (in_array('tasks.index', $permissions))
	<li>
		<a href="{{ route('tasks.index') }}"><i class="ti-check-box"></i><span>{{ _lang('Tasks') }}</span></a>
	</li>
	@endif
@endif

<li>
	<a href="javascript: void(0);"><i class="ti-shopping-cart"></i><span>{{ _lang('Products') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('products.create',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('products/create') }}">{{ _lang('Add New') }}</a></li>
		@endif
	  
		@if (in_array('products.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('products') }}">{{ _lang('Product List') }}</a></li>	
	    @endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="ti-agenda"></i><span>{{ _lang('Service') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('services.create',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('services/create') }}">{{ _lang('Add New') }}</a></li>
		@endif
		
		@if (in_array('services.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('services') }}">{{ _lang('Service List') }}</a></li>	
	    @endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="ti-truck"></i><span>{{ _lang('Supplier') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('suppliers.create',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('suppliers/create') }}">{{ _lang('Add New') }}</a></li>
		@endif
		
		@if (in_array('suppliers.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('suppliers') }}">{{ _lang('Supplier List') }}</a></li>	
	    @endif
	</ul>
</li>

@if( has_feature('inventory_module') )
<li>
	<a href="javascript: void(0);"><i class="ti-bag"></i><span>{{ _lang('Purchase') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('purchase_orders.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('purchase_orders') }}">{{ _lang('Purchase Orders') }}</a></li>
		@endif
	  
		@if (in_array('purchase_orders.create',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('purchase_orders/create') }}">{{ _lang('Create Purchase Order') }}</a></li>	
	    @endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="ti-back-left"></i><span>{{ _lang('Return') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('purchase_returns',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('purchase_returns') }}">{{ _lang('Purchase Return') }}</a></li>
		@endif
	  
		@if (in_array('sales_returns',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('sales_returns') }}">{{ _lang('Sales Return') }}</a></li>	
	    @endif
	</ul>
</li>
@endif

<li>
	<a href="javascript: void(0);"><i class="ti-shopping-cart-full"></i><span>{{ _lang('Sales') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if( has_feature('invoice_limit') )
			@if (in_array('invoices.create',$permissions))
				<li class="nav-item"><a class="nav-link" href="{{ url('invoices/create') }}">{{ _lang('Add Invoice') }}</a></li>
			@endif
			
			@if (in_array('invoices.index',$permissions))
				<li class="nav-item"><a class="nav-link" href="{{ url('invoices') }}">{{ _lang('Invoice List') }}</a></li>	
			@endif
		@endif
		
		@if( has_feature('quotation_limit') )
			@if (in_array('quotations.create',$permissions))
				<li class="nav-item"><a class="nav-link" href="{{ url('quotations/create') }}">{{ _lang('Add Quotation') }}</a></li>	
			@endif
		  
			@if (in_array('quotations.index',$permissions))
				<li class="nav-item"><a class="nav-link" href="{{ url('quotations') }}">{{ _lang('Quotation List') }}</a></li>	
		    @endif
	    @endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="ti-credit-card"></i><span>{{ _lang('Accounts') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('accounts.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('accounts') }}">{{ _lang('List Account') }}</a></li>
		@endif
	  
	    @if (in_array('accounts.create',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('accounts/create') }}">{{ _lang('Add New Account') }}</a></li>	
	    @endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="ti-receipt"></i><span>{{ _lang('Transactions') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('income.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('income') }}">{{ _lang('Income/Deposit') }}</a></li>
		@endif
		
		@if (in_array('expense.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('expense') }}">{{ _lang('Expense') }}</a></li>	
		@endif
		
		@if (in_array('transfer.create',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('transfer/create') }}">{{ _lang('Transfer') }}</a></li>	
		@endif
	  
		@if (in_array('income.income_calendar',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('income/calendar') }}">{{ _lang('Income Calendar') }}</a></li>	
		@endif
	  
	    @if (in_array('expense.expense_calendar',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('expense/calendar') }}">{{ _lang('Expense Calendar') }}</a></li>	
	    @endif
	</ul>
</li>

@if( has_feature('recurring_transaction') )
<li>
	<a href="javascript: void(0);"><i class="ti-wallet"></i><span>{{ _lang('Recurring Transaction') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('repeating_income.create',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('repeating_income/create') }}">{{ _lang('Add Repeating Income') }}</a></li>
		@endif
	  
		@if (in_array('repeating_income.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('repeating_income') }}">{{ _lang('Repeating Income List') }}</a></li>	
		@endif
	  
		@if (in_array('repeating_expense.create',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('repeating_expense/create') }}">{{ _lang('Add Repeating Expense') }}</a></li>	
		@endif
	  
		@if (in_array('repeating_expense.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('repeating_expense') }}">{{ _lang('Repeating Expense List') }}</a></li>	
	    @endif
	</ul>
</li>
@endif

@if(get_option('live_chat') == 'enabled' && has_feature('live_chat') )
	<li>
       <a href="{{ url('live_chat') }}"><i class="far fa-comment"></i><span>{{ _lang('Messenger') }}</span><span class="chat-notification {{ unread_message_count() > 0 ? 'show' : 'hidden' }}">{{ unread_message_count() }}</span></a>
	</li>
@endif

@if( has_feature('file_manager') )
	@if (in_array('file_manager.index',$permissions))
		<li>
			<a href="{{ url('file_manager') }}"><i class="ti-folder"></i><span>{{ _lang('File Manager') }}</span></a>
		</li>
	@endif
@endif

<li>
	<a href="javascript: void(0);"><i class="ti-bar-chart"></i><span>{{ _lang('Reports') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('reports.account_statement',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('reports/account_statement') }}">{{ _lang('Account Statement') }}</a></li>
		@endif
	  
		@if (in_array('reports.day_wise_income',$permissions))
		<li class="nav-item"><a class="nav-link" href="{{ url('reports/day_wise_income') }}">{{ _lang('Detail Income Report') }}</a></li>	
		@endif
	  
		@if (in_array('reports.date_wise_income',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('reports/date_wise_income') }}">{{ _lang('Date Wise Income') }}</a></li>	
		@endif
	  
		@if (in_array('reports.day_wise_expense',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('reports/day_wise_expense') }}">{{ _lang('Detail Expense Report') }}</a></li>	
		@endif
	  
	    @if (in_array('reports.date_wise_expense',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('reports/date_wise_expense') }}">{{ _lang('Date Wise Expense') }}</a></li>	
		@endif
	  
	    @if (in_array('reports.transfer_report',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('reports/transfer_report') }}">{{ _lang('Transfer Report') }}</a></li>	
		@endif
	  
	    @if (in_array('reports.income_vs_expense',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('reports/income_vs_expense') }}">{{ _lang('Income VS Expense') }}</a></li>	
		@endif
	  
		@if (in_array('reports.report_by_payer',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('reports/report_by_payer') }}">{{ _lang('Report By Payer') }}</a></li>	
		@endif
	  
	    @if (in_array('reports.report_by_payee',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('reports/report_by_payee') }}">{{ _lang('Report By Payee') }}</a></li>	
	    @endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="ti-settings"></i><span>{{ _lang('Settings') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if (in_array('company.change_settings',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('company/general_settings') }}">{{ _lang('Company Settings') }}</a></li>
		@endif
	  
	    @if (in_array('company_email_template.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('company_email_template') }}">{{ _lang('Email Template') }}</a></li>	
		@endif
	  
	    @if (in_array('chart_of_accounts.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('chart_of_accounts') }}">{{ _lang('Income & Expense Types') }}</a></li>	
		@endif
		
		@if (in_array('payment_methods.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('payment_methods') }}">{{ _lang('Payment Methods') }}</a></li>	
		@endif
		
		@if (in_array('product_units.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('product_units') }}">{{ _lang('Product Unit') }}</a></li>	
		@endif
	  
		@if (in_array('taxs.index',$permissions))
			<li class="nav-item"><a class="nav-link" href="{{ url('taxs') }}">{{ _lang('Tax Settings') }}</a></li>	
	    @endif
	</ul>
</li>