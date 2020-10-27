<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::group(['middleware' => ['install']], function () {	

	Route::get('/', 'WebsiteController@index');
	Route::get('sign_up', 'WebsiteController@sign_up');
	Route::get('site/{page}', 'WebsiteController@site');
	Route::post('emaiL_subscribed', 'WebsiteController@emaiL_subscribed');
	Route::post('contact/send_message', 'WebsiteController@send_message');

	Auth::routes(['verify' => true]);
	
	Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');
    Route::match(['get', 'post'],'register/client_signup','\App\Http\Controllers\Auth\RegisterController@client_signup');

	Route::group(['middleware' => ['auth','verified']], function () {
		
		Route::get('/dashboard', 'DashboardController@index');
		
		//Profile Controller
		Route::get('profile/edit', 'ProfileController@edit');
		Route::post('profile/update', 'ProfileController@update');
		Route::get('profile/change_password', 'ProfileController@change_password');
		Route::post('profile/update_password', 'ProfileController@update_password');
		

		//Membertship Controller
		Route::get('membership/my_subscription', 'MembershipController@my_subscription');  //View Subscription Details
		Route::get('membership/extend', 'MembershipController@extend');
		
		//Select Payment Gateway
		Route::post('membership/pay','MembershipController@pay');

		//Payment Gateway PayPal	
		Route::get('membership/paypal/{action?}','MembershipController@paypal');		
		
		//Payment Gateway Stripe
		Route::get('membership/stripe_payment/{action}/{payment_id?}','MembershipController@stripe_payment');	

		//Payment Gateway RazorPay
		Route::post('membership/razorpay_payment/{payment_id}','MembershipController@razorpay_payment');

		//Paystack Payment Gateway
		Route::get('membership/paystack_payment/{payment_id}/{reference}','MembershipController@paystack_payment');
		
		
		/** Admin Only Route **/
		Route::group(['middleware' => ['admin']], function () {
			//User Controller
			Route::get('users/type/{user_type}','UserController@index');
			Route::resource('users','UserController');
			

            //Payment Controller
			Route::get('offline_payment/create','PaymentController@create_offline_payment');
			Route::post('offline_payment/store','PaymentController@store_offline_payment');
			Route::get('members/payment_history','PaymentController@payment_history');

            //Email Subscribers
			Route::get('admin/email_subscribers','EmailSubscriberController@index');
			
			//Feature Controller
			Route::resource('features','FeatureController');

			//FAQ Controller
			Route::resource('faqs','FaqController');

			//Package Controller
			Route::resource('packages','PackageController');
			
			//Language Controller
			Route::resource('languages','LanguageController');	
			
			//Utility Controller
			Route::match(['get', 'post'],'administration/general_settings/{store?}', 'UtilityController@settings');
			Route::match(['get', 'post'],'administration/theme_option/{store?}', 'UtilityController@theme_option');
			Route::post('administration/upload_logo', 'UtilityController@upload_logo');
			Route::match(['get', 'patch'],'administration/currency_rates/{id?}', 'UtilityController@currency_rates');
			Route::get('administration/backup_database', 'UtilityController@backup_database');

			//Theme Option
			Route::match(['get', 'post'],'administration/theme_option/{store?}', 'UtilityController@theme_option');
			
			//Email Template
			Route::resource('email_templates','EmailTemplateController')->only([
				'index', 'show', 'edit', 'update'
			]);
			
		});
		
		Route::group(['middleware' => ['company']], function () {

			//Contact Group
			Route::resource('contact_groups','ContactGroupController');
		     
			//Contact Controller
			Route::match(['get', 'post'],'contacts/import','ContactController@import')->name('contacts.import');
			Route::get('contacts/get_table_data','ContactController@get_table_data');
			Route::post('contacts/send_email/{id}','ContactController@send_email')->name('contacts.send_email');
			Route::resource('contacts','ContactController');

			//Lead Controller
			Route::match(['get', 'post'],'leads/import','LeadController@import')->name('leads.import');
			Route::match(['get', 'post'],'leads/convert_to_customer/{id}','LeadController@convert_to_customer')->name('leads.convert_to_customer');
			Route::get('leads/delete_note/{id}','LeadController@delete_note')->name('leads.delete_note');
			Route::post('leads/create_note','LeadController@create_note')->name('leads.create_note');
			Route::get('leads/download_file/{file}','LeadController@download_file')->name('leads.download_file');
			Route::get('leads/delete_file/{id}','LeadController@delete_file')->name('leads.delete_file');
			Route::post('leads/upload_file','LeadController@upload_file')->name('leads.upload_file');
			Route::post('leads/get_table_data','LeadController@get_table_data');
			Route::get('leads/get_logs_data/{lead_id}','LeadController@get_logs_data');
			Route::get('leads/load_more_lead/{lead_status_id}/{last_lead_id}','LeadController@load_more_lead');
			Route::get('leads/update_lead_status/{lead_status_id}/{last_lead_id}','LeadController@update_lead_status');
			Route::get('leads/{view_type?}','LeadController@index')->where('view_type', 'kanban')->name('leads.index');
			Route::resource('leads','LeadController');

			//Project Controller
			Route::get('projects/delete_project_member/{member_id}','ProjectController@delete_project_member')->name('projects.delete_project_member');
			Route::get('projects/delete_note/{id}','ProjectController@delete_note')->name('projects.delete_note');
			Route::post('projects/create_note','ProjectController@create_note')->name('projects.create_note');
			Route::get('projects/download_file/{file}','ProjectController@download_file')->name('projects.download_file');
			Route::get('projects/delete_file/{id}','ProjectController@delete_file')->name('projects.delete_file');
			Route::post('projects/upload_file','ProjectController@upload_file')->name('projects.upload_file');
			Route::get('projects/get_logs_data/{id}','ProjectController@get_logs_data');
			Route::post('projects/get_table_data','ProjectController@get_table_data');
			Route::resource('projects','ProjectController');

			//Project Milestone
			Route::get('project_milestones/get_milestones/{project_id}','ProjectMilestoneController@get_milestones');	
			Route::resource('project_milestones','ProjectMilestoneController')->except(['index']);

			//TimeSheet Controller
			Route::resource('timesheets','TimeSheetController')->except(['index']);

			//Tasks Controller
			Route::post('tasks/get_table_data','TaskController@get_table_data');
			Route::get('tasks/load_more_task/{status_id}/{task_id}','TaskController@load_more_task');
			Route::get('tasks/update_task_status/{status_id}/{task_id}','TaskController@update_task_status');
			Route::get('tasks/{view_type?}','TaskController@index')->where('view_type', 'kanban')->name('tasks.index');			
			Route::resource('tasks','TaskController');

			//Account Controller	
			Route::resource('accounts','AccountController');
			

			//Income Controller
			Route::get('income/get_table_data','IncomeController@get_table_data');
			Route::get('income/calendar','IncomeController@calendar')->name('income.income_calendar');
			Route::resource('income','IncomeController');
			
			
			//Expense Controller
			Route::get('expense/get_table_data','ExpenseController@get_table_data');
			Route::get('expense/calendar','ExpenseController@calendar')->name('expense.expense_calendar');
			Route::resource('expense','ExpenseController');
			
			//Transfer Controller
			Route::get('transfer/create', 'TransferController@create')->name('transfer.create');
			Route::post('transfer/store', 'TransferController@store')->name('transfer.store');
			
			//Repeating Income
			Route::get('repeating_income/get_table_data','RepeatingIncomeController@get_table_data');
			Route::resource('repeating_income','RepeatingIncomeController');
			
			//Repeating Expense
			Route::get('repeating_expense/get_table_data','RepeatingExpenseController@get_table_data');
			Route::resource('repeating_expense','RepeatingExpenseController');

			//Chart Of Accounts
			Route::resource('chart_of_accounts','ChartOfAccountController');

			//Payment Method
			Route::resource('payment_methods','PaymentMethodController');
					
			//Supplier Controller
			Route::resource('suppliers','SupplierController');

			//Product Controller
			Route::get('products/get_product/{id}','ProductController@get_product');
			Route::match(['get', 'post'],'products/import','ProductController@import')->name('products.import');
			Route::resource('products','ProductController');

			//Product Controller
			Route::match(['get', 'post'],'services/import','ServiceController@import')->name('services.import');
			Route::resource('services','ServiceController');

			//Purchase Order
			Route::get('purchase_orders/create_payment/{id}','PurchaseController@create_payment')->name('purchase_orders.create_payment');
			Route::post('purchase_orders/store_payment','PurchaseController@store_payment')->name('purchase_orders.create_payment');
			Route::get('purchase_orders/view_payment/{id}','PurchaseController@view_payment')->name('purchase_orders.view_payment');
			Route::get('purchase_orders/download_pdf/{id}','PurchaseController@download_pdf')->name('purchase_orders.download_pdf');
			Route::resource('purchase_orders','PurchaseController');

			//Purchase Return
			Route::resource('purchase_returns','PurchaseReturnController');
			
			//Sales Return
			Route::resource('sales_returns','SalesReturnController');
					
			//Invoice Controller
			Route::get('invoices/create_payment/{id}','InvoiceController@create_payment')->name('invoices.create_payment');
			Route::post('invoices/store_payment','InvoiceController@store_payment')->name('invoices.create_payment');
			Route::get('invoices/mark_as_cancelled/{id}','InvoiceController@mark_as_cancelled')->name('invoices.mark_as_cancelled');
			Route::get('invoices/view_payment/{id}','InvoiceController@view_payment')->name('invoices.view_payment');
			Route::get('invoices/create_email/{invoice_id}','InvoiceController@create_email')->name('invoices.send_email');
			Route::post('invoices/send_email','InvoiceController@send_email')->name('invoices.send_email');
			Route::get('invoices/get_table_data','InvoiceController@get_table_data');
			Route::resource('invoices','InvoiceController');

			//Quotation Controller
			Route::get('quotations/convert_invoice/{quotation_id}','QuotationController@convert_invoice')->name('quotations.convert_invoice');
			Route::get('quotations/create_email/{quotation_id}','QuotationController@create_email')->name('quotations.send_email');
			Route::post('quotations/send_email','QuotationController@send_email')->name('quotations.send_email');
			Route::get('quotations/get_table_data','QuotationController@get_table_data');
			Route::resource('quotations','QuotationController');

			//Staff Controller
			Route::resource('staffs','StaffController');

			//User Roles
			Route::resource('roles','RoleController');
			
			//File Manager Controller
			Route::get('file_manager/directory/{parent_id}','FileManagerController@index')->name('file_manager.index');
			Route::get('file_manager/create_folder/{parent_id?}','FileManagerController@create_folder')->name('file_manager.create_folder');
			Route::post('file_manager/store_folder','FileManagerController@store_folder')->name('file_manager.create_folder');
			Route::get('file_manager/edit_folder/{id}','FileManagerController@edit_folder')->name('file_manager.edit_folder');
			Route::patch('file_manager/update_folder/{id}','FileManagerController@update_folder')->name('file_manager.edit_folder');
			Route::get('file_manager/create/{parent_id?}','FileManagerController@create')->name('file_manager.create');
			Route::resource('file_manager','FileManagerController');
			
			
			//Company Settings Controller
			Route::post('company/upload_logo', 'CompanySettingsController@upload_logo')->name('company.change_logo');
			Route::match(['get', 'post'],'company/general_settings/{store?}', 'CompanySettingsController@settings')->name('company.change_settings');

			Route::match(['get', 'post'],'company/crm_settings', 'CompanySettingsController@crm_settings')->name('company.crm_settings');
			
			//Lead Status Controller
			Route::get('lead_statuses/update_lead_status_order/{lead_status_id}/{order}','LeadStatusController@update_lead_status_order');
			Route::resource('lead_statuses','LeadStatusController')->except([
				'index'
			]);
			
			//Lead Source Controller
			Route::resource('lead_sources','LeadSourceController')->except([
				'index'
			]);


			//Task Status Controller
			Route::get('task_statuses/update_task_status_order/{task_status_id}/{order}','TaskStatusController@update_task_status_order');
			Route::resource('task_statuses','TaskStatusController')->except([
				'index'
			]);

			//Company Email Template
			Route::get('company_email_template/get_template/{id}','CompanyEmailTemplateController@get_template');
			Route::resource('company_email_template','CompanyEmailTemplateController');
			
			//Tax Controller
			Route::resource('taxs','TaxController');
			
			//Product Unit Controller
			Route::resource('product_units','ProductUnitController');
			
			//Permission Controller
			Route::get('permission/control/{user_id?}', 'PermissionController@index')->name('permission.manage');
			Route::post('permission/store', 'PermissionController@store')->name('permission.manage');

			
			//Report Controller
			Route::match(['get', 'post'],'reports/account_statement/{view?}', 'ReportController@account_statement')->name('reports.account_statement');
			Route::match(['get', 'post'],'reports/day_wise_income/{view?}', 'ReportController@day_wise_income')->name('reports.day_wise_income');
			Route::match(['get', 'post'],'reports/date_wise_income/{view?}', 'ReportController@date_wise_income')->name('reports.date_wise_income');
			Route::match(['get', 'post'],'reports/day_wise_expense/{view?}', 'ReportController@day_wise_expense')->name('reports.day_wise_expense');
			Route::match(['get', 'post'],'reports/date_wise_expense/{view?}', 'ReportController@date_wise_expense')->name('reports.date_wise_expense');
			Route::match(['get', 'post'],'reports/transfer_report/{view?}', 'ReportController@transfer_report')->name('reports.transfer_report');
			Route::match(['get', 'post'],'reports/income_vs_expense/{view?}', 'ReportController@income_vs_expense')->name('reports.income_vs_expense');
			Route::match(['get', 'post'],'reports/report_by_payer/{view?}', 'ReportController@report_by_payer')->name('reports.report_by_payer');
			Route::match(['get', 'post'],'reports/report_by_payee/{view?}', 'ReportController@report_by_payee')->name('reports.report_by_payee');

		});
		
		Route::group(['middleware' => ['client']], function () {
		    //Invoice
			Route::get('client/invoices/{status?}','ClientController@invoices');
		    
			//Quotation
			Route::get('client/quotations','ClientController@quotations');

		    //Projects
		    Route::get('client/projects','ClientController@projects');
		    Route::get('client/projects/{id}','ClientController@view_project');
		    Route::get('client/projects/delete_note/{id}','ClientController@delete_note');
			Route::post('client/projects/create_note','ClientController@create_note');
			Route::get('client/projects/download_file/{file}','ClientController@download_file');
			Route::get('client/projects/delete_file/{id}','ClientController@delete_file');
			Route::post('client/projects/upload_file','ClientController@upload_file');
		    
			//Transaction
			Route::get('client/transactions','ClientController@transactions');
			Route::get('client/view_transaction/{id}','ClientController@view_transaction');

            //Select Business
			Route::match(['get', 'post'],'client/select_business','ClientController@select_business');
		    
		});
		
		
		//Chat Controller
		Route::get('live_chat','ChatController@index');
		Route::post('live_chat/auth','ChatController@auth');
		Route::post('live_chat/send_message','ChatController@send_message');
		Route::get('live_chat/get_messages/{user_id}/{limit?}/{offset?}','ChatController@get_messages');
		Route::post('live_chat/mark_as_read/{sender_id}','ChatController@mark_as_read');
		Route::get('live_chat/notification_count','ChatController@notification_count');
		
		//Group Chat
		Route::get('live_chat/create_group','ChatController@create_group');
		Route::post('live_chat/store_group','ChatController@store_group');
		Route::get('live_chat/edit_group/{id}','ChatController@edit_group');
		Route::post('live_chat/update_group/{group_id}','ChatController@update_group');
		Route::get('live_chat/view_group_members/{id}','ChatController@view_group_members');
		Route::post('live_chat/send_group_message','ChatController@send_group_message');
		Route::get('live_chat/get_group_messages/{group_id}/{limit?}/{offset?}','ChatController@get_group_messages');
        Route::post('live_chat/mark_as_group_read/{group_id}','ChatController@mark_as_group_read');
        Route::get('live_chat/delete_group/{group_id}','ChatController@delete_group');
        Route::get('live_chat/left_group/{group_id}','ChatController@left_group');
		
	});
	
	//Convert Currency
	Route::get('convert_currency/{from}/{to}/{amount}','AccountController@convert_currency');
	
	//Get Client Info
	Route::get('contacts/get_client_info/{id}','ContactController@get_client_info');
	
	//Get Client Info
	Route::get('leads/get_lead_info/{id}','LeadController@get_lead_info');
	
	//Get Client Info
	Route::get('projects/get_project_info/{id}','ProjectController@get_project_info');
	
	//View Invoice & Quotation without login
	Route::get('client/view_invoice/{id}','ClientController@view_invoice');
	Route::get('client/view_quotation/{id}','ClientController@view_quotation');

	//Online Invoice Payment
	Route::get('client/invoice_payment/{id}/{payment_method}','ClientController@invoice_payment');
	
	//Stripe Payment Gateway
	Route::get('client/stripe_payment/{action}/{invoice_id}','ClientController@stripe_payment');

	//PayPal Payment Gateway
	Route::get('client/paypal/{action?}/{invoice_id?}','ClientController@paypal');	

	//Payment Gateway RazorPay
	Route::post('client/razorpay_payment/{invoice_id}','ClientController@razorpay_payment');

	//Paystack Payment Gateway
	Route::get('client/paystack_payment/{invoice_id}/{reference}','ClientController@paystack_payment');

	//Invoice & Quotation PDF Download
	Route::get('invoices/download_pdf/{id}','ClientController@download_invoice_pdf');
	Route::get('quotations/download_pdf/{id}','ClientController@download_quotation_pdf');

});

Route::get('/installation', 'Install\InstallController@index');
Route::get('install/database', 'Install\InstallController@database');
Route::post('install/process_install', 'Install\InstallController@process_install');
Route::get('install/create_user', 'Install\InstallController@create_user');
Route::post('install/store_user', 'Install\InstallController@store_user');
Route::get('install/system_settings', 'Install\InstallController@system_settings');
Route::post('install/finish', 'Install\InstallController@final_touch');		

//Ajax Select2 Controller
Route::get('ajax/get_table_data','Select2Controller@get_table_data');

//Show Notification
Route::get('notification/{id}','NotificationController@show')->middleware('auth');

//JSON data for dashboard chart
Route::get('dashboard/json_month_wise_income_expense','DashboardController@json_month_wise_income_expense')->middleware('auth');
Route::get('dashboard/json_income_vs_expense','DashboardController@json_income_vs_expense')->middleware('auth');


//Google Login
Route::get('google/redirect', 'Auth\SocialAuthGoogleController@redirect');
Route::get('google/callback', 'Auth\SocialAuthGoogleController@callback');

//Update System
Route::get('migration/update', 'Install\UpdateController@update_migration');

//PayPal IPN for Membership Payment
Route::post('membership/paypal_ipn','MembershipController@paypal_ipn');	

//PayPal IPN for Invoice Payment
Route::post('client/paypal_ipn','ClientController@paypal_ipn');

Route::get('console/run','CronJobsController@run');	