<?php

use Illuminate\Database\Seeder;

class UtilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		//Default Settings
		DB::table('settings')->insert([
			[
			  'name' => 'mail_type',
			  'value' => 'mail'
			],
			[
			  'name' => 'backend_direction',
			  'value' => 'ltr'
			],
			[
			  'name' => 'membership_system',
			  'value' => 'enabled'
			],
			[
			  'name' => 'trial_period',
			  'value' => '7'
			],
			[
			  'name' => 'allow_singup',
			  'value' => 'yes'
			],	
			[
			  'name' => 'email_verification',
			  'value' => 'disabled'
			],
			[
			  'name' => 'hero_title',
			  'value' => 'Start Your Business With Elit Kit'
			],	
			[
			  'name' => 'hero_sub_title',
			  'value' => 'A simple, easy to customize, and powerful business platform for managing and tracking Projects, Tasks, Invoices, Quotations, Leads, Customers, Transactions and many more!'
			],	
			[
			  'name' => 'meta_keywords',
			  'value' => 'invoice, projects, tasks, accounting, quotation, crm, business, erp, accounting software, live chat'
			],
			[
			  'name' => 'meta_description',
			  'value' => 'A simple, easy to customize, and powerful business platform for managing and tracking Projects, Tasks, Invoices, Quotations, Leads, Customers, Transactions and many more!'
			],		
		]);
		
		//Email Template
		DB::table('email_templates')->insert([
			[
			  'name' => 'registration',
			  'subject' => 'Registration Sucessfully',
			  'body' => '<div style="padding: 15px 30px;">
						 <h2 style="color: #555555;">Registration Successful</h2>
						 <p style="color: #555555;">Hi {name},<br /><span style="color: #555555;">Welcome to ElitKit and thank you for joining with us. You can now sign in to your account using your email and password.<br /><br />Regards<br />Tricky Code<br /></span></p>
						 </div>',
			],
			[
			  'name' => 'premium_membership',
			  'subject' => 'Premium Membership',
			  'body' => '<div style="padding: 15px 30px;">
						<h2 style="color: #555555; font-family: "PT Sans", sans-serif;">ElitKit Premium Subscription</h2>
						<p style="color: #555555; font-family: "PT Sans", sans-serif;">Hi {name},<br>
						<span style="color: #555555; font-family: "PT Sans", sans-serif;"><strong>Congratulations</strong> your paymnet was made sucessfully. Your current membership is valid <strong>until</strong> <strong>{valid_to}</strong></span><span style="color: #555555; font-family: "PT Sans", sans-serif;"><strong>.</strong>&nbsp;</span></p>
						<p><br style="color: #555555; font-family: "PT Sans", sans-serif;" /><span style="color: #555555; font-family: "PT Sans", sans-serif;">Thank You</span><br style="color: #555555; font-family: "PT Sans", sans-serif;" /><span style="color: #555555; font-family: "PT Sans", sans-serif;">Tricky Code</span></p>
						</div>',
			],
			[
			  'name' => 'alert_notification',
			  'subject' => 'ElitKit Renewals',
			  'body' => '<div style="padding: 15px 30px;">
							<h2 style="color: #555555; font-family: "PT Sans", sans-serif;">Account Renew Notification</h2>
							<p style="color: #555555; font-family: "PT Sans", sans-serif;">Hi {name},<br>
							<span style="color: #555555; font-family: "PT Sans", sans-serif;">Your package is due to <strong>expire on {valid_to}</strong> s</span><span style="color: #555555; font-family: "PT Sans", sans-serif;">o you will need to renew by then to keep your account active.</span></p>
							<p><br style="color: #555555; font-family: "PT Sans", sans-serif;" /><span style="color: #555555; font-family: "PT Sans", sans-serif;">Regards</span><br style="color: #555555; font-family: "PT Sans", sans-serif;" /><span style="color: #555555; font-family: "PT Sans", sans-serif;">Tricky Code</span></p>
							</div>',
			],			
		]);

		//Store Default Software Features
		DB::table('cm_features')->insert([
           [
              'icon' => "<i class='lni lni-package'></i>",
              'title' => "Easy Accounting",
              'content' => "Manage Account without any accounting knowledge",
           ],
           [
              'icon' => "<i class='lni lni-files'></i>",
              'title' => "Invoice",
              'content' => "Create professional Invoice and accept online payments",
           ],
           [
              'icon' => "<i class='lni lni-user'></i>",
              'title' => "CRM",
              'content' => "Contacts with Contact Group and Rich Customer Portal",
           ],
		    [
              'icon' => "<i class='lni lni-phone-set'></i>",
              'title' => "Leads",
              'content' => "Manage leads from different lead sources with kanban view",
           ],
		   [
              'icon' => "<i class='lni lni-briefcase'></i>",
              'title' => "Projects",
              'content' => "Manage different types of projects with milestone",
           ],
		   [
              'icon' => "<i class='lni lni-alarm'></i>",
              'title' => "Tasks",
              'content' => "Manage tasks with kanban view and assign task to staff",
           ],
           [
              'icon' => "<i class='lni lni-empty-file'></i>",
              'title' => "Quotation",
              'content' => "Create Professional Quotation for getting customer attention",
           ],
           [
              'icon' => "<i class='lni lni-facebook-messenger'></i>",
              'title' => "Live Chat",
              'content' => "Real time Chat with staffs, customers and private groups",
           ],
           [
              'icon' => "<i class='lni lni-credit-cards'></i>",
              'title' => "Online Payments",
              'content' => "Accept Online Payments from Clients",
           ],
		]);
		
		//Store Deafult Package
		DB::table('packages')->insert([
		    [
				'package_name'=>'Basic',
				'cost_per_month'=>'10.00',
				'cost_per_year'=>'99.00',
				'staff_limit'=>'a:2:{s:7:"monthly";s:1:"3";s:6:"yearly";s:2:"10";}',
				'contacts_limit'=>'a:2:{s:7:"monthly";s:2:"10";s:6:"yearly";s:2:"30";}',
				'invoice_limit'=>'a:2:{s:7:"monthly";s:2:"20";s:6:"yearly";s:3:"300";}',
				'quotation_limit'=>'a:2:{s:7:"monthly";s:2:"20";s:6:"yearly";s:3:"300";}',
				'project_management_module'=>'a:2:{s:7:"monthly";s:2:"No";s:6:"yearly";s:2:"No";}',
				'recurring_transaction'=>'a:2:{s:7:"monthly";s:2:"No";s:6:"yearly";s:2:"No";}',
				'live_chat'=>'a:2:{s:7:"monthly";s:2:"No";s:6:"yearly";s:2:"No";}',
				'file_manager'=>'a:2:{s:7:"monthly";s:2:"No";s:6:"yearly";s:2:"No";}',
				'inventory_module'=>'a:2:{s:7:"monthly";s:2:"No";s:6:"yearly";s:2:"No";}',
				'hrm_module'=>NULL,
				'payroll_module'=>NULL,
				'online_payment'=>'a:2:{s:7:"monthly";s:2:"No";s:6:"yearly";s:2:"No";}',
				'is_featured'=>0,
				'others'=>NULL,
			],
			[
				'package_name'=>'Standard',
				'cost_per_month'=>'25.00',
				'cost_per_year'=>'199.00',
				'staff_limit'=>'a:2:{s:7:"monthly";s:2:"10";s:6:"yearly";s:2:"20";}',
				'contacts_limit'=>'a:2:{s:7:"monthly";s:2:"30";s:6:"yearly";s:2:"50";}',
				'invoice_limit'=>'a:2:{s:7:"monthly";s:3:"300";s:6:"yearly";s:3:"500";}',
				'quotation_limit'=>'a:2:{s:7:"monthly";s:3:"300";s:6:"yearly";s:3:"500";}',
				'project_management_module'=>'a:2:{s:7:"monthly";s:3:"Yes";s:6:"yearly";s:3:"Yes";}',
				'recurring_transaction'=>'a:2:{s:7:"monthly";s:3:"Yes";s:6:"yearly";s:3:"Yes";}',
				'live_chat'=>'a:2:{s:7:"monthly";s:3:"Yes";s:6:"yearly";s:3:"Yes";}',
				'file_manager'=>'a:2:{s:7:"monthly";s:2:"No";s:6:"yearly";s:2:"No";}',
				'inventory_module'=>'a:2:{s:7:"monthly";s:3:"Yes";s:6:"yearly";s:3:"Yes";}',
				'hrm_module'=>NULL,
				'payroll_module'=>NULL,
				'online_payment'=>'a:2:{s:7:"monthly";s:2:"No";s:6:"yearly";s:2:"No";}',
				'is_featured'=>1,
				'others'=>NULL,
			],
			[
				'package_name'=>'Business Plus',
				'cost_per_month'=>'40.00',
				'cost_per_year'=>'399.00',
				'staff_limit'=>'a:2:{s:7:"monthly";s:2:"30";s:6:"yearly";s:9:"Unlimited";}',
				'contacts_limit'=>'a:2:{s:7:"monthly";s:9:"Unlimited";s:6:"yearly";s:9:"Unlimited";}',
				'invoice_limit'=>'a:2:{s:7:"monthly";s:3:"300";s:6:"yearly";s:9:"Unlimited";}',
				'quotation_limit'=>'a:2:{s:7:"monthly";s:3:"300";s:6:"yearly";s:9:"Unlimited";}',
				'project_management_module'=>'a:2:{s:7:"monthly";s:3:"Yes";s:6:"yearly";s:3:"Yes";}',
				'recurring_transaction'=>'a:2:{s:7:"monthly";s:3:"Yes";s:6:"yearly";s:3:"Yes";}',
				'live_chat'=>'a:2:{s:7:"monthly";s:3:"Yes";s:6:"yearly";s:3:"Yes";}',
				'file_manager'=>'a:2:{s:7:"monthly";s:3:"Yes";s:6:"yearly";s:3:"Yes";}',
				'inventory_module'=>'a:2:{s:7:"monthly";s:3:"Yes";s:6:"yearly";s:3:"Yes";}',
				'hrm_module'=>NULL,
				'payroll_module'=>NULL,
				'online_payment'=>'a:2:{s:7:"monthly";s:3:"Yes";s:6:"yearly";s:3:"Yes";}',
				'is_featured'=>0,
				'others'=>NULL,
			]
		]);
				
		//Store Default Currency Exchange Rate
		DB::table('currency_rates')->insert([
			[ 'id' => 1, 'currency' => 'AED', 'rate' => 4.101083 ],
			[ 'id' => 2, 'currency' => 'AFN', 'rate' => 85.378309 ],
			[ 'id' => 3, 'currency' => 'ALL', 'rate' => 123.510844 ],
			[ 'id' => 4, 'currency' => 'AMD', 'rate' => 548.849773 ],
			[ 'id' => 5, 'currency' => 'ANG', 'rate' => 2.008050 ],
			[ 'id' => 6, 'currency' => 'AOA', 'rate' => 556.155120 ],
			[ 'id' => 7, 'currency' => 'ARS', 'rate' => 70.205746 ],
			[ 'id' => 8, 'currency' => 'AUD', 'rate' => 1.809050 ],
			[ 'id' => 9, 'currency' => 'AWG', 'rate' => 2.009782 ],
			[ 'id' => 10, 'currency' => 'AZN', 'rate' => 1.833159 ],
			[ 'id' => 11, 'currency' => 'BAM', 'rate' => 1.966840 ],
			[ 'id' => 12, 'currency' => 'BBD', 'rate' => 2.245460 ],
			[ 'id' => 13, 'currency' => 'BDT', 'rate' => 95.162306 ],
			[ 'id' => 14, 'currency' => 'BGN', 'rate' => 1.952383 ],
			[ 'id' => 15, 'currency' => 'BHD', 'rate' => 0.421787 ],
			[ 'id' => 16, 'currency' => 'BIF', 'rate' => 2117.865003 ],
			[ 'id' => 17, 'currency' => 'BMD', 'rate' => 1.116545 ],
			[ 'id' => 18, 'currency' => 'BND', 'rate' => 1.583270 ],
			[ 'id' => 19, 'currency' => 'BOB', 'rate' => 7.718004 ],
			[ 'id' => 20, 'currency' => 'BRL', 'rate' => 5.425949 ],
			[ 'id' => 21, 'currency' => 'BSD', 'rate' => 1.121775 ],
			[ 'id' => 22, 'currency' => 'BTC', 'rate' => 0.000244 ],
			[ 'id' => 23, 'currency' => 'BTN', 'rate' => 82.818317 ],
			[ 'id' => 24, 'currency' => 'BWP', 'rate' => 12.683055 ],
			[ 'id' => 25, 'currency' => 'BYN', 'rate' => 2.621037 ],
			[ 'id' => 26, 'currency' => 'BYR', 'rate' => 9999.999999 ],
			[ 'id' => 27, 'currency' => 'BZD', 'rate' => 2.261248 ],
			[ 'id' => 28, 'currency' => 'CAD', 'rate' => 1.552879 ],
			[ 'id' => 29, 'currency' => 'CDF', 'rate' => 1898.127343 ],
			[ 'id' => 30, 'currency' => 'CHF', 'rate' => 1.056023 ],
			[ 'id' => 31, 'currency' => 'CLF', 'rate' => 0.033950 ],
			[ 'id' => 32, 'currency' => 'CLP', 'rate' => 936.781769 ],
			[ 'id' => 33, 'currency' => 'CNY', 'rate' => 7.827878 ],
			[ 'id' => 34, 'currency' => 'COP', 'rate' => 4491.872864 ],
			[ 'id' => 35, 'currency' => 'CRC', 'rate' => 635.520417 ],
			[ 'id' => 36, 'currency' => 'CUC', 'rate' => 1.116545 ],
			[ 'id' => 37, 'currency' => 'CUP', 'rate' => 29.588450 ],
			[ 'id' => 38, 'currency' => 'CVE', 'rate' => 110.887227 ],
			[ 'id' => 39, 'currency' => 'CZK', 'rate' => 26.906059 ],
			[ 'id' => 40, 'currency' => 'DJF', 'rate' => 198.432393 ],
			[ 'id' => 41, 'currency' => 'DKK', 'rate' => 7.472892 ],
			[ 'id' => 42, 'currency' => 'DOP', 'rate' => 60.196240 ],
			[ 'id' => 43, 'currency' => 'DZD', 'rate' => 134.499489 ],
			[ 'id' => 44, 'currency' => 'EGP', 'rate' => 17.585483 ],
			[ 'id' => 45, 'currency' => 'ERN', 'rate' => 16.748349 ],
			[ 'id' => 46, 'currency' => 'ETB', 'rate' => 36.696587 ],
			[ 'id' => 47, 'currency' => 'EUR', 'rate' => 1.000000 ],
			[ 'id' => 48, 'currency' => 'FJD', 'rate' => 2.549240 ],
			[ 'id' => 49, 'currency' => 'FKP', 'rate' => 0.908257 ],
			[ 'id' => 50, 'currency' => 'GBP', 'rate' => 0.907964 ],
			[ 'id' => 51, 'currency' => 'GEL', 'rate' => 3.115301 ],
			[ 'id' => 52, 'currency' => 'GGP', 'rate' => 0.908257 ],
			[ 'id' => 53, 'currency' => 'GHS', 'rate' => 6.220337 ],
			[ 'id' => 54, 'currency' => 'GIP', 'rate' => 0.908257 ],
			[ 'id' => 55, 'currency' => 'GMD', 'rate' => 56.605069 ],
			[ 'id' => 56, 'currency' => 'GNF', 'rate' => 9999.999999 ],
			[ 'id' => 57, 'currency' => 'GTQ', 'rate' => 8.576324 ],
			[ 'id' => 58, 'currency' => 'GYD', 'rate' => 234.489495 ],
			[ 'id' => 59, 'currency' => 'HKD', 'rate' => 8.674753 ],
			[ 'id' => 60, 'currency' => 'HNL', 'rate' => 27.678062 ],
			[ 'id' => 61, 'currency' => 'HRK', 'rate' => 7.590196 ],
			[ 'id' => 62, 'currency' => 'HTG', 'rate' => 106.356510 ],
			[ 'id' => 63, 'currency' => 'HUF', 'rate' => 341.150311 ],
			[ 'id' => 64, 'currency' => 'IDR', 'rate' => 9999.999999 ],
			[ 'id' => 65, 'currency' => 'ILS', 'rate' => 4.159226 ],
			[ 'id' => 66, 'currency' => 'IMP', 'rate' => 0.908257 ],
			[ 'id' => 67, 'currency' => 'INR', 'rate' => 82.763894 ],
			[ 'id' => 68, 'currency' => 'IQD', 'rate' => 1339.198712 ],
			[ 'id' => 69, 'currency' => 'IRR', 'rate' => 9999.999999 ],
			[ 'id' => 70, 'currency' => 'ISK', 'rate' => 151.202539 ],
			[ 'id' => 71, 'currency' => 'JEP', 'rate' => 0.908257 ],
			[ 'id' => 72, 'currency' => 'JMD', 'rate' => 151.606351 ],
			[ 'id' => 73, 'currency' => 'JOD', 'rate' => 0.791685 ],
			[ 'id' => 74, 'currency' => 'JPY', 'rate' => 118.278988 ],
			[ 'id' => 75, 'currency' => 'KES', 'rate' => 115.283224 ],
			[ 'id' => 76, 'currency' => 'KGS', 'rate' => 81.395812 ],
			[ 'id' => 77, 'currency' => 'KHR', 'rate' => 4603.144194 ],
			[ 'id' => 78, 'currency' => 'KMF', 'rate' => 495.355724 ],
			[ 'id' => 79, 'currency' => 'KPW', 'rate' => 1004.922902 ],
			[ 'id' => 80, 'currency' => 'KRW', 'rate' => 1372.190164 ],
			[ 'id' => 81, 'currency' => 'KWD', 'rate' => 0.344879 ],
			[ 'id' => 82, 'currency' => 'KYD', 'rate' => 0.934921 ],
			[ 'id' => 83, 'currency' => 'KZT', 'rate' => 456.318281 ],
			[ 'id' => 84, 'currency' => 'LAK', 'rate' => 9978.233671 ],
			[ 'id' => 85, 'currency' => 'LBP', 'rate' => 1696.373291 ],
			[ 'id' => 86, 'currency' => 'LKR', 'rate' => 206.967335 ],
			[ 'id' => 87, 'currency' => 'LRD', 'rate' => 221.076044 ],
			[ 'id' => 88, 'currency' => 'LSL', 'rate' => 18.121543 ],
			[ 'id' => 89, 'currency' => 'LTL', 'rate' => 3.296868 ],
			[ 'id' => 90, 'currency' => 'LVL', 'rate' => 0.675387 ],
			[ 'id' => 91, 'currency' => 'LYD', 'rate' => 1.557311 ],
			[ 'id' => 92, 'currency' => 'MAD', 'rate' => 10.730569 ],
			[ 'id' => 93, 'currency' => 'MDL', 'rate' => 19.734707 ],
			[ 'id' => 94, 'currency' => 'MGA', 'rate' => 4165.265277 ],
			[ 'id' => 95, 'currency' => 'MKD', 'rate' => 61.516342 ],
			[ 'id' => 96, 'currency' => 'MMK', 'rate' => 1566.586511 ],
			[ 'id' => 97, 'currency' => 'MNT', 'rate' => 3088.650418 ],
			[ 'id' => 98, 'currency' => 'MOP', 'rate' => 8.975925 ],
			[ 'id' => 99, 'currency' => 'MRO', 'rate' => 398.607011 ],
			[ 'id' => 100, 'currency' => 'MUR', 'rate' => 43.205754 ],
			[ 'id' => 101, 'currency' => 'MVR', 'rate' => 17.250725 ],
			[ 'id' => 102, 'currency' => 'MWK', 'rate' => 825.239292 ],
			[ 'id' => 103, 'currency' => 'MXN', 'rate' => 24.963329 ],
			[ 'id' => 104, 'currency' => 'MYR', 'rate' => 4.810633 ],
			[ 'id' => 105, 'currency' => 'MZN', 'rate' => 73.591410 ],
			[ 'id' => 106, 'currency' => 'NAD', 'rate' => 18.121621 ],
			[ 'id' => 107, 'currency' => 'NGN', 'rate' => 408.099790 ],
			[ 'id' => 108, 'currency' => 'NIO', 'rate' => 37.844015 ],
			[ 'id' => 109, 'currency' => 'NOK', 'rate' => 11.405599 ],
			[ 'id' => 110, 'currency' => 'NPR', 'rate' => 132.508354 ],
			[ 'id' => 111, 'currency' => 'NZD', 'rate' => 1.847363 ],
			[ 'id' => 112, 'currency' => 'OMR', 'rate' => 0.429801 ],
			[ 'id' => 113, 'currency' => 'PAB', 'rate' => 1.121880 ],
			[ 'id' => 114, 'currency' => 'PEN', 'rate' => 3.958258 ],
			[ 'id' => 115, 'currency' => 'PGK', 'rate' => 3.838505 ],
			[ 'id' => 116, 'currency' => 'PHP', 'rate' => 57.698037 ],
			[ 'id' => 117, 'currency' => 'PKR', 'rate' => 176.121721 ],
			[ 'id' => 118, 'currency' => 'PLN', 'rate' => 4.386058 ],
			[ 'id' => 119, 'currency' => 'PYG', 'rate' => 7386.917924 ],
			[ 'id' => 120, 'currency' => 'QAR', 'rate' => 4.065302 ],
			[ 'id' => 121, 'currency' => 'RON', 'rate' => 4.826717 ],
			[ 'id' => 122, 'currency' => 'RSD', 'rate' => 117.627735 ],
			[ 'id' => 123, 'currency' => 'RUB', 'rate' => 83.568390 ],
			[ 'id' => 124, 'currency' => 'RWF', 'rate' => 1067.822267 ],
			[ 'id' => 125, 'currency' => 'SAR', 'rate' => 4.190432 ],
			[ 'id' => 126, 'currency' => 'SBD', 'rate' => 9.235251 ],
			[ 'id' => 127, 'currency' => 'SCR', 'rate' => 14.529548 ],
			[ 'id' => 128, 'currency' => 'SDG', 'rate' => 61.772847 ],
			[ 'id' => 129, 'currency' => 'SEK', 'rate' => 10.785247 ],
			[ 'id' => 130, 'currency' => 'SGD', 'rate' => 1.587844 ],
			[ 'id' => 131, 'currency' => 'SHP', 'rate' => 0.908257 ],
			[ 'id' => 132, 'currency' => 'SLL', 'rate' => 9999.999999 ],
			[ 'id' => 133, 'currency' => 'SOS', 'rate' => 653.732410 ],
			[ 'id' => 134, 'currency' => 'SRD', 'rate' => 8.327212 ],
			[ 'id' => 135, 'currency' => 'STD', 'rate' => 9999.999999 ],
			[ 'id' => 136, 'currency' => 'SVC', 'rate' => 9.816821 ],
			[ 'id' => 137, 'currency' => 'SYP', 'rate' => 575.019506 ],
			[ 'id' => 138, 'currency' => 'SZL', 'rate' => 18.038821 ],
			[ 'id' => 139, 'currency' => 'THB', 'rate' => 35.884679 ],
			[ 'id' => 140, 'currency' => 'TJS', 'rate' => 10.875343 ],
			[ 'id' => 141, 'currency' => 'TMT', 'rate' => 3.907909 ],
			[ 'id' => 142, 'currency' => 'TND', 'rate' => 3.186636 ],
			[ 'id' => 143, 'currency' => 'TOP', 'rate' => 2.635661 ],
			[ 'id' => 144, 'currency' => 'TRY', 'rate' => 7.131927 ],
			[ 'id' => 145, 'currency' => 'TTD', 'rate' => 7.585158 ],
			[ 'id' => 146, 'currency' => 'TWD', 'rate' => 33.739208 ],
			[ 'id' => 147, 'currency' => 'TZS', 'rate' => 2582.397529 ],
			[ 'id' => 148, 'currency' => 'UAH', 'rate' => 29.335146 ],
			[ 'id' => 149, 'currency' => 'UGX', 'rate' => 4169.685347 ],
			[ 'id' => 150, 'currency' => 'USD', 'rate' => 1.116545 ],
			[ 'id' => 151, 'currency' => 'UYU', 'rate' => 48.718630 ],
			[ 'id' => 152, 'currency' => 'UZS', 'rate' => 9999.999999 ],
			[ 'id' => 153, 'currency' => 'VEF', 'rate' => 11.151499 ],
			[ 'id' => 154, 'currency' => 'VND', 'rate' => 9999.999999 ],
			[ 'id' => 155, 'currency' => 'VUV', 'rate' => 133.944917 ],
			[ 'id' => 156, 'currency' => 'WST', 'rate' => 3.074259 ],
			[ 'id' => 157, 'currency' => 'XAF', 'rate' => 659.652615 ],
			[ 'id' => 158, 'currency' => 'XAG', 'rate' => 0.088073 ],
			[ 'id' => 159, 'currency' => 'XAU', 'rate' => 0.000756 ],
			[ 'id' => 160, 'currency' => 'XCD', 'rate' => 3.017519 ],
			[ 'id' => 161, 'currency' => 'XDR', 'rate' => 0.809234 ],
			[ 'id' => 162, 'currency' => 'XOF', 'rate' => 659.646672 ],
			[ 'id' => 163, 'currency' => 'XPF', 'rate' => 119.931356 ],
			[ 'id' => 164, 'currency' => 'YER', 'rate' => 279.475009 ],
			[ 'id' => 165, 'currency' => 'ZAR', 'rate' => 18.603040 ],
			[ 'id' => 166, 'currency' => 'ZMK', 'rate' => 9999.999999 ],
			[ 'id' => 167, 'currency' => 'ZMW', 'rate' => 17.892580 ],
			[ 'id' => 168, 'currency' => 'ZWL', 'rate' => 359.527584 ],
		]);
		
    }
}
