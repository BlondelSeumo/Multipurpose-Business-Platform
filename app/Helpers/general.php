<?php

if ( ! function_exists('_lang')){
	function _lang($string = ''){

		$action = app('request')->route()->getAction();
		$controller = class_basename($action['controller']);
        $target_lang = '';

		if(explode('@', $controller)[0] == 'WebsiteController'){
            $target_lang = session('language') == '' ? get_option('language') : session('language');
			if(session('language') == ''){
				session(['language' => $target_lang]);
			}
		}else{
			$target_lang = get_language();
		}		
		
		if($target_lang == ''){
			$target_lang = "language";
		}
		
		if(file_exists(resource_path() . "/language/$target_lang.php")){
			include(resource_path() . "/language/$target_lang.php"); 
		}else{
			include(resource_path() . "/language/language.php"); 
		}
		
		if (array_key_exists($string,$language)){
			return $language[$string];
		}else{
			return $string;
		}
	}
}


if ( ! function_exists('_dlang')){
	function _dlang( $string = '' ){
		
		//Get Target language
		$target_lang = get_option('language');

		if(company_id() !=''){
			$target_lang = get_company_option('language');
		}

		if($target_lang == ''){
			$target_lang = 'language';
		}
		
		if(file_exists(resource_path() . "/language/$target_lang.php")){
			include(resource_path() . "/language/$target_lang.php"); 
		}else{
			include(resource_path() . "/language/language.php"); 
		}
		
		if (array_key_exists( $string, $language )){
			return $language[$string];
		}else{
			return $string;
		}
	}
}


if ( ! function_exists('startsWith')){
	function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
}

/*------------ Chat Functions --------------*/

if ( ! function_exists('get_last_message')){
	function get_last_message($sender)
	{
		 date_default_timezone_set(get_option('timezone'));
		 $me = Auth::user()->id;
		 $message = DB::select("SELECT chat_messages.* FROM chat_messages WHERE (chat_messages.from = $sender AND chat_messages.to = $me) OR (chat_messages.from = $me AND chat_messages.to = $sender) ORDER BY chat_messages.id DESC LIMIT 1");
	     
	     //return $message;
		 if ( $message != NULL ) {
			 if($message[0]->from == $sender){
				$string = '<p class="preview">'. $message[0]->message .'</p>
				<p class="float-right time-ago">'. \Carbon\Carbon::parse($message[0]->created_at)->diffForHumans(null,true,true) .' ago</p>';
				 
				return $string; 
			 }else{
				$string = '<p class="preview">'._lang('You').': '.$message[0]->message .'</p>
				<p class="float-right time-ago">'. \Carbon\Carbon::parse($message[0]->created_at)->diffForHumans(null,true,true) .' ago</p>';
				
				return $string;
			 }
		 }
		 return '<p class="preview">'._lang('No Message Found').'</p>';
	}
}

if ( ! function_exists('get_chat_order')){
	function get_chat_order()
	{
		 $company_id = company_id();
		 $me = Auth::user()->id;
		 if(Auth::user()->user_type != 'client'){
		 	$messages = DB::select("SELECT MAX(id) as id,(chat_messages.from + chat_messages.to) as chat_session,chat_messages.from, chat_messages.to FROM chat_messages WHERE chat_messages.from = $me OR chat_messages.to = $me AND company_id = $company_id GROUP BY chat_session ORDER BY id DESC");
		}else{
 			$messages = DB::select("SELECT MAX(id) as id,(chat_messages.from + chat_messages.to) as chat_session,chat_messages.from, chat_messages.to FROM chat_messages WHERE chat_messages.from = $me OR chat_messages.to = $me GROUP BY chat_session ORDER BY id DESC");
		}

		 $message_array = array();
		 $i = 1;
		 foreach($messages as $msg){
   
			if ( ! array_key_exists($msg->from,$message_array) && ! array_key_exists($msg->to,$message_array)){			    	
				if($msg->from == $me){
				   $message_array[$msg->to] = $i;
				}else if($msg->to == $me){
				   $message_array[$msg->from] = $i;
				}	
				$i++;
			}
		 }
		return $message_array; 
		 
	}
}

if ( ! function_exists('unread_message_count')){
	function unread_message_count()
	{
		$me = Auth::user()->id;
		$groups = \App\User::find($me)->chat_groups;
	    $my_groups = $groups->pluck('id')->toArray();
		
		//User to User message Count
		$message = DB::select("SELECT COUNT(id) as c FROM chat_messages WHERE chat_messages.to = $me AND status=0");
	    
        //Group message count
		$group_message_count = 0;
		if(! empty($my_groups)){	
            $my_groups = implode(",",$my_groups);	
			
			$group_message = Db::select("SELECT COUNT(group_chat_messages.id) as c FROM group_chat_messages WHERE group_chat_messages.group_id IN ($my_groups) AND group_chat_messages.sender_id != $me AND group_chat_messages.id NOT IN (SELECT message_id FROM group_chat_message_status WHERE user_id = $me)");		
            $group_message_count = $group_message[0]->c;
		}
		 
		return $message[0]->c + $group_message_count; 		 
		 
	}
}

if ( ! function_exists('group_message_count')){
	function group_message_count($group_id)
	{
		$me = Auth::user()->id;
		$group_message = Db::select("SELECT COUNT(group_chat_messages.id) as c FROM group_chat_messages WHERE group_chat_messages.group_id = $group_id AND group_chat_messages.sender_id != $me AND group_chat_messages.id NOT IN (SELECT message_id FROM group_chat_message_status WHERE user_id=$me)");		
		return $group_message[0]->c; 		 	 
	}
}

if ( ! function_exists('get_group_chat_order')){
	function get_group_chat_order()
	{
		 $company_id = company_id();
		 //$me = Auth::user()->id;
		 if(Auth::user()->user_type != 'client'){
		 	$messages = DB::select("SELECT MAX(id) as id, group_id FROM group_chat_messages WHERE company_id = $company_id GROUP BY group_id ORDER BY id DESC");
		 }else{
		 	 $company_ids = Auth::user()->client->pluck('company_id')->toArray();
		 	 $company_ids = implode(",",$company_ids);
		 	 $messages = DB::select("SELECT MAX(id) as id, group_id FROM group_chat_messages WHERE company_id IN ($company_ids) GROUP BY group_id ORDER BY id DESC");
		 }
		 
		 $group_message_array = array();
		 $index = 1;
		 foreach($messages as $msg){
   
			if ( ! array_key_exists($msg->group_id,$group_message_array)){			    	
				$group_message_array[$msg->group_id] = $index;
				$index++;
			}
		 }
		return $group_message_array; 
		 
	}
}

if ( ! function_exists('get_last_group_message')){
	function get_last_group_message($group_id)
	{
		 $me = Auth::user()->id;
		 $message = DB::select("SELECT * FROM group_chat_messages WHERE group_id = $group_id ORDER BY id DESC LIMIT 1");
	     //return $message;
		 if ( $message != NULL ) { 
			return $message[0]->message;
		 }
		 return _lang('No Message Found');
	}
}

if ( ! function_exists('get_initials')){
	function get_initials($string){
		$words = explode(" ", $string);
		$initials = null;
		foreach ($words as $w) {
			 $initials .= $w[0];
		}
		return $initials;
	}
}


if ( ! function_exists('create_option')){
	function create_option($table,$value,$display,$selected="",$where=NULL){
		$options = '';
		$condition = '';
		if($where != NULL){
			$condition .= "WHERE ";
			foreach( $where as $key => $v ){
				$condition.=$key."'".$v."' ";
			}
		}
        
		if (is_array($display)){
		   $display_array =  $display;
		   $display =  $display_array[0];
		   $display1 =  $display_array[1];
		}
		
		$query = DB::select("SELECT * FROM $table $condition");
		foreach($query as $d){
			if( $selected!="" && $selected == $d->$value ){   
				if(! isset($display_array)){
					$options.="<option value='".$d->$value."' selected='true'>".ucwords($d->$display)."</option>";
			    }else{
					$options.="<option value='".$d->$value."' selected='true'>".ucwords($d->$display.' - '.$d->$display1)."</option>";
				}
			}else{
				if(! isset($display_array)){
					$options.="<option value='".$d->$value."'>".ucwords($d->$display)."</option>";
			    }else{
					$options.="<option value='".$d->$value."'>".ucwords($d->$display.' - '.$d->$display1)."</option>";
				}
			} 
		}
		
		echo $options;
	}
}

if ( ! function_exists('object_to_string')){
	function object_to_string($object,$col,$quote = false) 
	{
		$string = "";
		foreach($object as $data){
			if($quote == true){
				$string .="'".$data->$col."', ";
			}else{
				$string .=$data->$col.", ";
			}
		}
		$string = substr_replace($string, "", -2);
		return $string;
	}
}

if ( ! function_exists('get_table')){
	function get_table($table,$where=NULL) 
	{
		$condition = "";
		if($where != NULL){
			$condition .= "WHERE ";
			foreach( $where as $key => $v ){
				$condition.=$key."'".$v."' ";
			}
		}
		$query = DB::select("SELECT * FROM $table $condition");
		return $query;
	}
}


if ( ! function_exists('user_count')){
	function user_count($user_type) 
	{
		$count = \App\User::where("user_type",$user_type)
						->selectRaw("COUNT(id) as total")
						->first()->total;
	    return $count;
	}
}


if ( ! function_exists('has_permission')){
	function has_permission($name) 
	{				
		$permission_list = \Auth::user()->role->permissions;
		$permission = $permission_list->firstWhere('permission', $name);

	    if ( $permission != null ) {
		   return true;
		}
		return false;
	}
}

if ( ! function_exists('permission_list')){
	function permission_list()
	{
		  
		$permission_list =  \App\AccessControl::where("role_id", Auth::user()->role_id)
											  ->pluck('permission')->toArray();	
	    return $permission_list;
	}
}


if ( ! function_exists('get_logo')){
	function get_logo() 
	{
		$logo = get_option("logo");
		if($logo ==""){
			return asset("public/images/company-logo.png");
		}
		return asset("public/uploads/media/$logo"); 
	}
}

if ( ! function_exists('get_favicon')){
	function get_favicon() 
	{
		$favicon = get_option("favicon");
		if($favicon == ""){
			return asset("public/images/favicon.png");
		}
		return asset("public/uploads/media/$favicon"); 
	}
}


if ( ! function_exists('sql_escape')){
	function sql_escape($unsafe_str) 
	{
		if (get_magic_quotes_gpc())
		{
			$unsafe_str = stripslashes($unsafe_str);
		}
		return $escaped_str = str_replace("'", "", $unsafe_str);
	}
}

if ( ! function_exists('get_option')){
	function get_option($name, $optional = '' ) 
	{
		$setting = DB::table('settings')->where('name', $name)->get();
	    if ( ! $setting->isEmpty() ) {
		   return $setting[0]->value;
		}
		return $optional;

	}
}

if ( ! function_exists('get_array_option')){
	function get_array_option($name, $key = '', $optional = '' ) 
	{
		if($key == ''){
			if(session('language') == ''){
				if(company_id() == ''){
					$key = get_option('language');
				}else{
					$key = get_company_option('language');
				}
                
                session(['language' => $key]);
			}else{
				$key = session('language');
			}
		}
		$setting = DB::table('settings')->where('name', $name)->get();
	    if ( ! $setting->isEmpty() ) {

		   $value =  $setting[0]->value;
		   if(@unserialize($value) !== false){
		   	   $value =  @unserialize($setting[0]->value);

		   	   return isset($value[$key]) ? $value[$key] : $value[array_key_first($value)];
		   }

		   return $value;
		}
		return $optional;

	}
}

if ( ! function_exists('get_array_data')){
	function get_array_data($data, $key = '') 
	{
       if($key == ''){
			if(session('language') == ''){
				if(company_id() == ''){
					$key = get_option('language');
				}else{
					$key = get_company_option('language');
				}
                
                session(['language' => $key]);
			}else{
				$key = session('language');
			}
		}
		
	   if(@unserialize($data) !== false){
	   	   $value =  @unserialize($data);

	   	   return isset($value[$key]) ? $value[$key] : $value[array_key_first($value)];
	   }

	   return $data;

	}
}


if ( ! function_exists('update_option')){
	function update_option($name, $value) 
	{
		date_default_timezone_set(get_option('timezone','Asia/Dhaka'));
		
	    $data = array();
		$data['value'] = $value; 
		$data['updated_at'] = \Carbon\Carbon::now();
		if(\App\Setting::where('name', $name)->exists()){				
			\App\Setting::where('name', $name)->update($data);			
		}else{
			$data['name'] = $name; 
			$data['created_at'] = \Carbon\Carbon::now();
			\App\Setting::insert($data); 
		}
	}
}


if ( ! function_exists('timezone_list'))
{

 function timezone_list() {
  $zones_array = array();
  $timestamp = time();
  foreach(timezone_identifiers_list() as $key => $zone) {
    date_default_timezone_set($zone);
    $zones_array[$key]['ZONE'] = $zone;
    $zones_array[$key]['GMT'] = 'UTC/GMT ' . date('P', $timestamp);
  }
  return $zones_array;
}

}

if ( ! function_exists('create_timezone_option'))
{

 function create_timezone_option($old="") {
  $option = "";
  $timestamp = time();
  foreach(timezone_identifiers_list() as $key => $zone) {
    date_default_timezone_set($zone);
	$selected = $old == $zone ? "selected" : "";
	$option .= '<option value="'. $zone .'"'.$selected.'>'. 'GMT ' . date('P', $timestamp) .' '.$zone.'</option>';
  }
  echo $option;
}

}


if ( ! function_exists( 'get_country_list' ))
{
    function get_country_list( $old_data='' ) {
		if( $old_data == '' ){
			echo file_get_contents( app_path().'/Helpers/country.txt' );
		}else{
			$pattern='<option value="'.$old_data.'">';
			$replace='<option value="'.$old_data.'" selected="selected">';
			$country_list=file_get_contents( app_path().'/Helpers/country.txt' );
			$country_list=str_replace($pattern,$replace,$country_list);
			echo $country_list;
		}
    }	
}

if ( ! function_exists('decimalPlace'))
{
 function decimalPlace($number, $symbol = '', $format = '', $position = ''){
	
	if($format == ''){
		$base_currency = \Cache::get('base_currency'.session('company_id'));
	}else{
		$base_currency = $format;	
	}
	
	if(session('company_id') == ''){
		session(['company_id' => company_id()]);
	}
	
	if($base_currency == ''){
		$base_currency = base_currency();
		\Cache::put('base_currency'.session('company_id'),$base_currency);
	}
    
	//Currency position Left or Right
    if($position == ''){
    	$currency_position = \Cache::get('currency_position'.session('company_id'));
    }else{
    	$currency_position = $position;
    }
	
	if($currency_position == ''){
		$currency_position = get_company_option('currency_position', get_option('currency_position','left'));
		\Cache::put('currency_position'.session('company_id'),$currency_position);
	}
	
	if($symbol == ''){
		return money_format_2( $number, $base_currency );
	}
		
	if($currency_position == 'right'){	
		return money_format_2( $number, $base_currency ).' '.$symbol;	
	}else{
		return $symbol.' '.money_format_2( $number, $base_currency );
	}
	
	//return number_format((float)$number, 2);
 }

}

/* Method use for Global amount only */
if ( ! function_exists('g_decimal_place'))
{

 function g_decimal_place($number, $symbol = '', $format = ''){
	if($format == ''){
		$base_currency = \Cache::get('base_currency');
	}else{
		$base_currency = $format;	
	}
	
	$currency_position = \Cache::get('currency_position');
	
	
	if($base_currency == ''){
		$base_currency = get_option('currency','USD');
		\Cache::put('base_currency',$base_currency);
	}
	
	if($currency_position == ''){
		$currency_position = get_option('currency_position','left');
		\Cache::put('currency_position',$currency_position);
	}
	
	if($symbol == ''){
		return money_format_2( $number, $base_currency );
	}
		
	if($currency_position == 'left'){	
		return $symbol.' '.money_format_2( $number, $base_currency );
	}else{
		return money_format_2( $number, $base_currency ).' '.$symbol;
	}
	
 }

}

if (!function_exists('money_format_2')) {
	function money_format_2($floatcurr, $curr = 'USD'){
		$currencies['ARS'] = array(2, ',', '.');          //  Argentine Peso
		$currencies['AMD'] = array(2, '.', ',');          //  Armenian Dram
		$currencies['AWG'] = array(2, '.', ',');          //  Aruban Guilder
		$currencies['AUD'] = array(2, '.', ' ');          //  Australian Dollar
		$currencies['BSD'] = array(2, '.', ',');          //  Bahamian Dollar
		$currencies['BHD'] = array(3, '.', ',');          //  Bahraini Dinar
		$currencies['BDT'] = array(2, '.', ',');          //  Bangladesh, Taka
		$currencies['BZD'] = array(2, '.', ',');          //  Belize Dollar
		$currencies['BMD'] = array(2, '.', ',');          //  Bermudian Dollar
		$currencies['BOB'] = array(2, '.', ',');          //  Bolivia, Boliviano
		$currencies['BAM'] = array(2, '.', ',');          //  Bosnia and Herzegovina, Convertible Marks
		$currencies['BWP'] = array(2, '.', ',');          //  Botswana, Pula
		$currencies['BRL'] = array(2, ',', '.');          //  Brazilian Real
		$currencies['BND'] = array(2, '.', ',');          //  Brunei Dollar
		$currencies['CAD'] = array(2, '.', ',');          //  Canadian Dollar
		$currencies['KYD'] = array(2, '.', ',');          //  Cayman Islands Dollar
		$currencies['CLP'] = array(0,  '', '.');          //  Chilean Peso
		$currencies['CNY'] = array(2, '.', ',');          //  China Yuan Renminbi
		$currencies['COP'] = array(2, ',', '.');          //  Colombian Peso
		$currencies['CRC'] = array(2, ',', '.');          //  Costa Rican Colon
		$currencies['HRK'] = array(2, ',', '.');          //  Croatian Kuna
		$currencies['CUC'] = array(2, '.', ',');          //  Cuban Convertible Peso
		$currencies['CUP'] = array(2, '.', ',');          //  Cuban Peso
		$currencies['CYP'] = array(2, '.', ',');          //  Cyprus Pound
		$currencies['CZK'] = array(2, '.', ',');          //  Czech Koruna
		$currencies['DKK'] = array(2, ',', '.');          //  Danish Krone
		$currencies['DOP'] = array(2, '.', ',');          //  Dominican Peso
		$currencies['XCD'] = array(2, '.', ',');          //  East Caribbean Dollar
		$currencies['EGP'] = array(2, '.', ',');          //  Egyptian Pound
		$currencies['SVC'] = array(2, '.', ',');          //  El Salvador Colon
		$currencies['ATS'] = array(2, ',', '.');          //  Euro
		$currencies['BEF'] = array(2, ',', '.');          //  Euro
		$currencies['DEM'] = array(2, ',', '.');          //  Euro
		$currencies['EEK'] = array(2, ',', '.');          //  Euro
		$currencies['ESP'] = array(2, ',', '.');          //  Euro
		$currencies['EUR'] = array(2, ',', '.');          //  Euro
		$currencies['FIM'] = array(2, ',', '.');          //  Euro
		$currencies['FRF'] = array(2, ',', '.');          //  Euro
		$currencies['GRD'] = array(2, ',', '.');          //  Euro
		$currencies['IEP'] = array(2, ',', '.');          //  Euro
		$currencies['ITL'] = array(2, ',', '.');          //  Euro
		$currencies['LUF'] = array(2, ',', '.');          //  Euro
		$currencies['NLG'] = array(2, ',', '.');          //  Euro
		$currencies['PTE'] = array(2, ',', '.');          //  Euro
		$currencies['GHC'] = array(2, '.', ',');          //  Ghana, Cedi
		$currencies['GIP'] = array(2, '.', ',');          //  Gibraltar Pound
		$currencies['GTQ'] = array(2, '.', ',');          //  Guatemala, Quetzal
		$currencies['HNL'] = array(2, '.', ',');          //  Honduras, Lempira
		$currencies['HKD'] = array(2, '.', ',');          //  Hong Kong Dollar
		$currencies['HUF'] = array(0,  '', '.');          //  Hungary, Forint
		$currencies['ISK'] = array(0,  '', '.');          //  Iceland Krona
		$currencies['INR'] = array(2, '.', ',');          //  Indian Rupee
		$currencies['IDR'] = array(2, ',', '.');          //  Indonesia, Rupiah
		$currencies['IRR'] = array(2, '.', ',');          //  Iranian Rial
		$currencies['JMD'] = array(2, '.', ',');          //  Jamaican Dollar
		$currencies['JPY'] = array(0,  '', ',');          //  Japan, Yen
		$currencies['JOD'] = array(3, '.', ',');          //  Jordanian Dinar
		$currencies['KES'] = array(2, '.', ',');          //  Kenyan Shilling
		$currencies['KWD'] = array(3, '.', ',');          //  Kuwaiti Dinar
		$currencies['LVL'] = array(2, '.', ',');          //  Latvian Lats
		$currencies['LBP'] = array(0,  '', ' ');          //  Lebanese Pound
		$currencies['LTL'] = array(2, ',', ' ');          //  Lithuanian Litas
		$currencies['MKD'] = array(2, '.', ',');          //  Macedonia, Denar
		$currencies['MYR'] = array(2, '.', ',');          //  Malaysian Ringgit
		$currencies['MTL'] = array(2, '.', ',');          //  Maltese Lira
		$currencies['MUR'] = array(0,  '', ',');          //  Mauritius Rupee
		$currencies['MXN'] = array(2, '.', ',');          //  Mexican Peso
		$currencies['MZM'] = array(2, ',', '.');          //  Mozambique Metical
		$currencies['NPR'] = array(2, '.', ',');          //  Nepalese Rupee
		$currencies['ANG'] = array(2, '.', ',');          //  Netherlands Antillian Guilder
		$currencies['ILS'] = array(2, '.', ',');          //  New Israeli Shekel
		$currencies['TRY'] = array(2, '.', ',');          //  New Turkish Lira
		$currencies['NZD'] = array(2, '.', ',');          //  New Zealand Dollar
		$currencies['NOK'] = array(2, ',', '.');          //  Norwegian Krone
		$currencies['PKR'] = array(2, '.', ',');          //  Pakistan Rupee
		$currencies['PEN'] = array(2, '.', ',');          //  Peru, Nuevo Sol
		$currencies['UYU'] = array(2, ',', '.');          //  Peso Uruguayo
		$currencies['PHP'] = array(2, '.', ',');          //  Philippine Peso
		$currencies['PLN'] = array(2, '.', ' ');          //  Poland, Zloty
		$currencies['GBP'] = array(2, '.', ',');          //  Pound Sterling
		$currencies['OMR'] = array(3, '.', ',');          //  Rial Omani
		$currencies['RON'] = array(2, ',', '.');          //  Romania, New Leu
		$currencies['ROL'] = array(2, ',', '.');          //  Romania, Old Leu
		$currencies['RUB'] = array(2, ',', '.');          //  Russian Ruble
		$currencies['SAR'] = array(2, '.', ',');          //  Saudi Riyal
		$currencies['SGD'] = array(2, '.', ',');          //  Singapore Dollar
		$currencies['SKK'] = array(2, ',', ' ');          //  Slovak Koruna
		$currencies['SIT'] = array(2, ',', '.');          //  Slovenia, Tolar
		$currencies['ZAR'] = array(2, '.', ' ');          //  South Africa, Rand
		$currencies['KRW'] = array(0,  '', ',');          //  South Korea, Won
		$currencies['SZL'] = array(2, '.', ', ');         //  Swaziland, Lilangeni
		$currencies['SEK'] = array(2, ',', '.');          //  Swedish Krona
		$currencies['CHF'] = array(2, '.', '\'');         //  Swiss Franc
		$currencies['TZS'] = array(2, '.', ',');          //  Tanzanian Shilling
		$currencies['THB'] = array(2, '.', ',');          //  Thailand, Baht
		$currencies['TOP'] = array(2, '.', ',');          //  Tonga, Paanga
		$currencies['AED'] = array(2, '.', ',');          //  UAE Dirham
		$currencies['UAH'] = array(2, ',', ' ');          //  Ukraine, Hryvnia
		$currencies['USD'] = array(2, '.', ',');          //  US Dollar
		$currencies['VUV'] = array(0,  '', ',');          //  Vanuatu, Vatu
		$currencies['VEF'] = array(2, ',', '.');          //  Venezuela Bolivares Fuertes
		$currencies['VEB'] = array(2, ',', '.');          //  Venezuela, Bolivar
		$currencies['VND'] = array(0,  '', '.');          //  Viet Nam, Dong
		$currencies['ZWD'] = array(2, '.', ' ');          //  Zimbabwe Dollar
		$currencies['XOF'] = array(2, '.', ' ');          //  West African CFA franc
		
		
		if (array_key_exists($curr,$currencies)){
			return number_format($floatcurr, $currencies[$curr][0], $currencies[$curr][1], $currencies[$curr][2]);
		}else{
			return number_format($floatcurr, $currencies['USD'][0], $currencies['USD'][1], $currencies['USD'][2]); 
		}
		
	}
}

if( !function_exists('formatinr') ){
	// custom function to generate: ##,##,###.##
	function formatinr($input)
	{
		$dec = "";
		$pos = strpos($input, ".");
		if ($pos === FALSE)
		{
			//no decimals
		}
		else
		{
			//decimals
			$dec   = substr(round(substr($input, $pos), 2), 1);
			$input = substr($input, 0, $pos);
		}
		$num   = substr($input, -3);    // get the last 3 digits
		$input = substr($input, 0, -3); // omit the last 3 digits already stored in $num
		// loop the process - further get digits 2 by 2
		while (strlen($input) > 0)
		{
			$num   = substr($input, -2).",".$num;
			$input = substr($input, 0, -2);
		}
		return $num.$dec;
	}
}

if( !function_exists('load_language') ){
	function load_language($active=''){
		$path = resource_path() . "/language";
		$files = scandir($path);
		$options="";
		
		foreach($files as $file){
		    $name = pathinfo($file, PATHINFO_FILENAME);
			if($name == "." || $name == "" || $name == "language"){
				continue;
			}
			
			$selected = "";
			if($active == $name){
				$selected = "selected";
			}else{
				$selected = "";
			}
			
			$options .= "<option value='$name' $selected>".$name."</option>";
		        
		}
		echo $options;
	}
}

if( !function_exists('get_language_list') ){
	function get_language_list(){
		$path = resource_path() . "/language";
		$files = scandir($path);
		$array = array();
		
		foreach($files as $file){
		    $name = pathinfo($file, PATHINFO_FILENAME);
			if($name == "." || $name == "" || $name == "language" || $name == "flags"){
				continue;
			}
	
			$array[] = $name;
		        
		}
		return $array;
	}
}

if( !function_exists('process_string') ){

 function process_string($search_replace,$string){
   $result = $string;
   foreach($search_replace as $key=>$value){
		$result = str_replace($key,$value,$result);
   }
   return $result;
 }

}

/** Company Functions **/

if ( ! function_exists('company_id')){
	function company_id()
	{
		if(Auth::check()){
			if( Auth::user()->company_id != ''){	 
				return Auth::user()->company_id; 
			}else if(Auth::user()->company_id == '' || Auth::user()->user_type == 'client'){
				// Return company id from session
				return session('company_id');
			}else if(Auth::user()->company_id == '' || Auth::user()->user_type == 'admin'){
                return '';
			}
		}
		return ''; 
	}
}

if ( ! function_exists('get_company_logo')){
	function get_company_logo($company_id = '') 
	{
		if($company_id == ''){
			$logo = get_company_option("company_logo");
		}else{
			$logo = get_company_field($company_id,"company_logo");
		}
		if($logo == ''){
			return get_logo();
		}
		return asset("public/uploads/company/$logo"); 
	}
}

if ( ! function_exists('get_company_option')){
	function get_company_option($name, $optional='') 
	{
		$setting = DB::table('company_settings')
					->where('name', $name)
					->where('company_id', company_id())
					->get();
					
	    if ( ! $setting->isEmpty() ) {
		   return $setting[0]->value;
		}
		return $optional;

	}
}

if ( ! function_exists('get_company_field')){
	function get_company_field($company_id, $name, $optional='') 
	{
		$setting = DB::table('company_settings')
					->where('name', $name)
					->where('company_id', $company_id)
					->get();
					
	    if ( ! $setting->isEmpty() ) {
		   return $setting[0]->value;
		}
		return $optional;

	}
}

if ( ! function_exists('has_membership_system')){
	function has_membership_system()
	{
		$membership_system = \Cache::get('membership_system');	
		if($membership_system == ''){
			$membership_system = get_option('membership_system');
			\Cache::put('membership_system',$membership_system);
		}
		
		return $membership_system;
	}
}

if ( ! function_exists('membership_validity')){
	function membership_validity()
	{
		/*$valid_to = session('valid_to');
		
		if($valid_to == ''){
			$valid_to = Auth::user()->company->valid_to;
			session(['valid_to' => $valid_to]);
		}*/
		
		$valid_to = Auth::user()->company->valid_to;
		
		return $valid_to;	
	}
}

if ( ! function_exists('currency')){
	function currency($currency = '')
	{ 
		if($currency == ''){
			$currency = get_company_option( 'base_currency', get_option('currency','USD') );
		}
		
		return html_entity_decode(get_currency_symbol( $currency ), ENT_QUOTES, 'UTF-8');
	
		//return get_company_option("currency_symbol", Auth::user()->currency);
	}
}

if ( ! function_exists('base_currency')){
	function base_currency()
	{ 
		$currency = get_company_option( 'base_currency', get_option('currency','USD') );
		return $currency;
	}
}


/** End Company Functions **/


if ( ! function_exists( 'get_currency_list' ))
{
	function get_currency_list( $old_data='', $serialize = false ) {	
		$currency_list = file_get_contents( app_path().'/Helpers/currency.txt' );
		
		if( $old_data == "" ){
			echo $currency_list;
		}else{
			if($serialize == true){
				$old_data = unserialize($old_data);
				for($i=0; $i<count($old_data); $i++){
					$pattern = '<option value="'.$old_data[$i].'">';
					$replace = '<option value="'.$old_data[$i].'" selected="selected">';
				    $currency_list = str_replace($pattern,$replace,$currency_list);
				}
				echo $currency_list;
			}else{
				$pattern = '<option value="'.$old_data.'">';
				$replace = '<option value="'.$old_data.'" selected="selected">';
				$currency_list = str_replace($pattern,$replace,$currency_list);
				echo $currency_list;
			}
		}
	}	
}

if ( ! function_exists( 'get_currency_symbol' ))
{
	function get_currency_symbol( $currency_code ) {
		include(app_path().'/Helpers/currency_symbol.php');
        
		if (array_key_exists($currency_code, $currency_symbols)){
			return $currency_symbols[$currency_code];
		}
		return "";
		
	}
}	

if ( ! function_exists('current_day_income')){
	function current_day_income()
	{ 
	    $compan_id = company_id();
		$date = date("Y-m-d");
		
		$query = DB::select("SELECT IFNULL(SUM(base_amount),0) as total FROM transactions 
		WHERE trans_date='$date' AND dr_cr='cr' AND company_id='$compan_id'");
        return $query[0]->total;		
	}
}

if ( ! function_exists('current_day_expense')){
	function current_day_expense()
	{ 
	    $compan_id = company_id();
		$date = date("Y-m-d");
		
		$query = DB::select("SELECT IFNULL(SUM(base_amount),0) as total FROM transactions 
		WHERE trans_date='$date' AND dr_cr='dr' AND company_id='$compan_id'");
        return $query[0]->total;		
	}
}

if ( ! function_exists('current_month_income')){
	function current_month_income()
	{ 
	    $compan_id = company_id();
		$month = date('m');
		$year = date('Y');
		
		$monthly_income = \App\Transaction::selectRaw("IFNULL(SUM(base_amount),0) as total")
										   ->where("dr_cr","cr")
										   ->where("company_id",$compan_id)
										   ->whereMonth("trans_date",$month) 
										   ->whereYear("trans_date",$year) 														
										   ->first()->total;
        return 	$monthly_income;					
	}
}

if ( ! function_exists('current_month_expense')){
	function current_month_expense()
	{ 
	    $compan_id = company_id();
		$month = date('m');
		$year = date('Y');
		
		$monthly_expense = \App\Transaction::selectRaw("IFNULL(SUM(base_amount),0) as total")
											->where("dr_cr","dr")
											->where("company_id",$compan_id)
											->whereMonth("trans_date",$month) 
											->whereYear("trans_date",$year) 														
											->first()->total;
        return 	$monthly_expense;		
	}
}


if ( ! function_exists('get_financial_balance'))
{

 function get_financial_balance(){
   $company_id = company_id();
 
   $result = DB::select("SELECT b.*,((SELECT IFNULL(opening_balance,0) 
   FROM accounts WHERE id = b.id)+(SELECT IFNULL(SUM(amount),0) 
   FROM transactions WHERE dr_cr = 'cr' AND account_id = b.id))-(SELECT IFNULL(SUM(amount),0) 
   FROM transactions WHERE dr_cr = 'dr' AND account_id = b.id) as balance 
   FROM accounts as b WHERE b.company_id='$company_id'");
   return $result;

 }

}

if ( ! function_exists('invoice_status')){
	function invoice_status($status)
	{
		if($status == 'Unpaid'){
			return "<span class='badge badge-danger'>$status</span>"; 
		}else if($status == 'Paid'){
			return "<span class='badge badge-success'>$status</span>"; 
		}else if($status == 'Partially_Paid'){
			return "<span class='badge badge-warning'>". str_replace('_',' ',$status)."</span>"; 
		}else if($status == 'Canceled'){
			return "<span class='badge badge-danger'>$status</span>"; 
		}
	}
}

if ( ! function_exists('status')){
	function status($status, $class='success')
	{
		if($class == 'danger'){
			return "<span class='badge badge-danger'>$status</span>"; 
		}else if($class == 'success'){
			return "<span class='badge badge-success'>$status</span>"; 
		}else if($class == 'info'){
			return "<span class='badge badge-dark'>$status</span>"; 
		}
	}
}

if ( ! function_exists('project_status')){
	function project_status($status)
	{
		$uc_status = ucwords(str_replace('_',' ',$status));

		if($status == 'not_started'){
			return "<span class='badge badge-info'>$uc_status</span>"; 
		}else if($status == 'in_progress'){
			return "<span class='badge badge-primary'>$uc_status</span>"; 
		}else if($status == 'on_hold'){
			return "<span class='badge badge-warning'>$uc_status</span>"; 
		}else if($status == 'cancelled'){
			return "<span class='badge badge-danger'>$uc_status</span>"; 
		}else if($status == 'completed'){
			return "<span class='badge badge-success'>$uc_status</span>"; 
		}
	}
}

if ( ! function_exists('task_priority')){
	function task_priority($priority)
	{
		if($priority == 'low'){
			return "<span class='badge badge-primary'>".ucwords($priority)."</span>"; 
		}else if($priority == 'medium'){
			return "<span class='badge badge-warning'>".ucwords($priority)."</span>"; 
		}else if($priority == 'high'){
			return "<span class='badge badge-danger'>".ucwords($priority)."</span>"; 
		}
	}
}


if ( ! function_exists('payment_method')){
	function payment_method($pm, $company_id)
	{	
	   $payment_method = \App\PaymentMethod::where('name', $pm)->where("company_id",$company_id);
	   if($payment_method->exists()){
	      return $payment_method->first()->id;
	   }else{
		  $payment_method = new \App\PaymentMethod();
		  $payment_method->name = $pm;
		  $payment_method->company_id = $company_id;
		  $payment_method->save();
		  return $payment_method->id;
	   }
	}
}	

if ( ! function_exists('increment_invoice_number')){
	function increment_invoice_number()
	{
        $company_id = company_id();		
		$data = array();
        $data['value'] = get_company_option('invoice_starting',1001) + 1; 
        $data['company_id'] = $company_id; 
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        if(\App\CompanySetting::where('name', "invoice_starting")->where("company_id",$company_id)->exists()){				
           \App\CompanySetting::where('name','invoice_starting')
                         ->where("company_id",$company_id)
                         ->update($data);			
        }else{
           $data['name'] = 'invoice_starting'; 
           $data['created_at'] = date('Y-m-d H:i:s');
           \App\CompanySetting::insert($data); 
        }
	}
}

if ( ! function_exists('file_icon')){
	function file_icon($mime_type)
    {
        static $font_awesome_file_icon_classes = [
            // Images
            'image'=> 'fa-file-image',
            // Audio
            'audio'=> 'fa-file-audio',
            // Video
            'video'=> 'fa-file-video',
            // Documents
            'application/pdf'=> 'fa-file-pdf',
            'application/msword'=> 'fa-file-word',
            'application/vnd.ms-word'=> 'fa-file-word',
            'application/vnd.oasis.opendocument.text'=> 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml'=> 'fa-file-word',
            'application/vnd.ms-excel'=> 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml'=> 'fa-file-excel',
            'application/vnd.oasis.opendocument.spreadsheet'=> 'fa-file-excel',
            'application/vnd.ms-powerpoint'=> 'fa-file-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml'=> 'ffa-file-powerpoint',
            'application/vnd.oasis.opendocument.presentation'=> 'fa-file-powerpoint',
            'text/plain'=> 'fa-file-alt',
            'text/html'=> 'fa-file-code',
            'application/json'=> 'fa-file-code',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=> 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=> 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'=> 'fa-file-powerpoint',
            // Archives
            'application/gzip'=> 'fa-file-archive',
            'application/zip'=> 'fa-file-archive',
            'application/x-zip-compressed'=> 'fa-file-archive',
            // Misc
            'application/octet-stream'=> 'fa-file-archive',
        ];

        if (isset($font_awesome_file_icon_classes[$mime_type]))
            return $font_awesome_file_icon_classes[$mime_type];

        $mime_group = explode('/', $mime_type, 2)[0];
        return (isset($font_awesome_file_icon_classes[$mime_group])) ? $font_awesome_file_icon_classes[$mime_group] : 'fa-file';
    }
}

if ( ! function_exists('chat_user_list')){
	function chat_user_list()
	{
		$company_id = company_id();
		$user_id = Auth::user()->id;
		$data = array();
		
		if(Auth::user()->user_type != 'client'){
			$unreadMessages = DB::table('chat_messages')
								->select('from','to', DB::raw('COUNT(id) as unread_message_count'))   
								->where('company_id', $company_id)
								->where('status', 0)
								->groupBy('from');
			   
			
			$data['staffs'] = DB::table('users')
								->leftJoinSub($unreadMessages, 'unread_messages', function ($join) use($user_id) {
									$join->on('users.id', '=', 'unread_messages.from')
										 ->where('unread_messages.to',$user_id);
								})
								->where('user_type','!=','client')
								->whereRaw("company_id = $company_id AND id != $user_id")
								->get();
								
			
			$data['clients'] = DB::table('users')
								   ->leftJoinSub($unreadMessages, 'unread_messages', function ($join) use($user_id) {
									   $join->on('users.id', '=', 'unread_messages.from')
											->where('unread_messages.to',$user_id);
								   })
								   ->select('users.*','unread_messages.*')
								   ->join('contacts','contacts.user_id','users.id')
								   ->where('user_type','client')
								   ->where('contacts.company_id',$company_id)
								   ->where('users.id','!=',$user_id)
								   ->get();
							   
        }else{
        	$company_ids = Auth::user()->client->pluck('company_id');

        	$unreadMessages = DB::table('chat_messages')
								->select('from','to', DB::raw('COUNT(id) as unread_message_count'))   
								->whereIn('company_id', $company_ids)
								->where('status', 0)
								->groupBy('from');
		   
		
			$data['staffs'] = DB::table('users')
								->leftJoinSub($unreadMessages, 'unread_messages', function ($join) use($user_id) {
									$join->on('users.id', '=', 'unread_messages.from')
										 ->where('unread_messages.to',$user_id);
								})
								->where('user_type','!=','client')
								->whereIn('company_id', $company_ids)
								->get();
        }

		$data['chat_groups'] = \App\User::find($user_id)->chat_groups;			

		return $data;
	}
}

/**Get All Invoice template **/
if( ! function_exists('get_invoice_templates') ){
    function get_invoice_templates(){
    	//Builtin Templates
    	$system_templates = array(
            'classic' 		=> _lang('Classic'), 
            'classic-red'   => _lang('Classic Red'), 
            'modern'        => _lang('Modern'), 
            'general'       => _lang('General'), 
    	);

    	$templates = App\InvoiceTemplate::where('company_id',company_id())->get();

    	foreach($templates as $template){
			$system_templates[$template->id] = $template->name .' (' . _lang('Custom') . ')';
    	}

    	return $system_templates;
    }
}

/**Get All Quotation template **/
if( ! function_exists('get_quotation_templates') ){
    function get_quotation_templates(){
    	//Builtin Templates
    	$system_templates = array(
            'classic' 		=> _lang('Classic'), 
            'classic-red'   => _lang('Classic Red'), 
            'modern'        => _lang('Modern'), 
            'general'       => _lang('General'), 
    	);

    	return $system_templates;
    }
}

if ( ! function_exists('update_currency_exchange_rate')){
	function update_currency_exchange_rate($force = false)
	{
		date_default_timezone_set(get_option('timezone','Asia/Dhaka'));

		$currency_update_time = \Cache::get('currency_update_time');	
		
		if($currency_update_time == ''){
			$currency_update_time = get_option( 'currency_update_time', date("Y-m-d H:i:s", strtotime('-24 hours', time())) );
		    \Cache::put('currency_update_time', $currency_update_time);
		}
		
		$start  = new \Carbon\Carbon( $currency_update_time );
		$end    = \Carbon\Carbon::now();
  
		$last_run = $start->diffInHours($end);

		if( $last_run >= 12 || $force == true){
			// set API Endpoint and API key 
			$endpoint = 'latest';
			$access_key = get_option('fixer_api_key');

			// Initialize CURL:
			$ch = curl_init('http://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.'');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			// Store the data:
			$json = curl_exec($ch);
			curl_close($ch);

			// Decode JSON response:
			$exchangeRates = json_decode($json, true);

			if($exchangeRates['success'] == false){
				return false;
			}

			$base_currency =  $exchangeRates['base'];
			
			$currency_rates = array();
			
			foreach($exchangeRates['rates'] as $currency => $rate){
				$currency_rates[] = array(
										"currency" => $currency, 
										"rate" => $rate,
										"created_at" => date('Y-m-d H:i:s'),
										"updated_at" => date('Y-m-d H:i:s')
									);
				//echo $currency." - ".$rate."<br>";
			}
			
			DB::beginTransaction();
			
			\App\CurrencyRate::getQuery()->delete();
			
			DB::statement("ALTER TABLE currency_rates AUTO_INCREMENT = 1");
			
			\App\CurrencyRate::insert($currency_rates);
			
			//Store Last Update time
			update_option("currency_update_time", \Carbon\Carbon::now());
			
			\Cache::put('currency_update_time', \Carbon\Carbon::now());
			
			DB::commit();
		}
	}
}

if ( ! function_exists('convert_currency'))
{
    function convert_currency($from_currency, $to_currency, $amount){
		$currency1 = \App\CurrencyRate::where('currency',$from_currency)->first()->rate;
		$currency2 = \App\CurrencyRate::where('currency',$to_currency)->first()->rate;

		$converted_output = ($amount/$currency1) * $currency2;
        return $converted_output;
    }
}

if ( ! function_exists('convert_currency_2'))
{
    function convert_currency_2($currency1_rate, $currency2_rate, $amount){
		$currency1 = $currency1_rate;
		$currency2 = $currency2_rate;

		$converted_output = ($amount/$currency1) * $currency2;
        return $converted_output;
    }
}


/** Update Package Usages **/
if ( ! function_exists('update_package_limit'))
{
    function update_package_limit( $feature, $limit = 1 ){
		$company = Auth::user()->company;
		if( $company->$feature != 'Yes' && $company->$feature != 'Unlimited'){
            $current_limit = (int) $company->$feature;
            $company->$feature = $current_limit - 1;
            $company->save();
		}
    }
}


/** Check Feature Limit **/
if ( ! function_exists('has_feature'))
{
    function has_feature( $feature, $limit = false ){
    	if( has_membership_system() != 'enabled' ){
           return true;
    	}
    	
        if(Auth::user()->user_type == 'client'){
        	return true;
        }

		$company = Auth::user()->company;

		if( $company->$feature == 'Yes' && $company->feature == 'Unlimited'){
           return true;
		}else if( $company->$feature == 'No' OR $company->$feature == null){
		   return false;
		}else{
			return true;
		}

		return false;		
    }
}


/** Check Feature Limit **/
if ( ! function_exists('has_feature_limit'))
{
    function has_feature_limit( $feature ){
		$company = Auth::user()->company;

		$current_limit = $company->$feature;
		
		if($current_limit == 'Unlimited'){
			return true;
		}
		
		$current_limit = (int) $company->$feature;

		if($current_limit > 0){
			return true;
		}

		return false;		
    }
}

/** Check Feature Limit **/
if ( ! function_exists('available_limit'))
{
    function available_limit( $feature, $limit = 1 ){
		$company = Auth::user()->company;

		$current_limit = $company->$feature;
		
		if($current_limit == 'Unlimited'){
			return true;
		}
		
		$current_limit = (int) $company->$feature;

		if($current_limit >= $limit){
			return true;
		}

		return false;		
    }
}

/** get Currenct theme asset **/
if ( ! function_exists('theme_asset'))
{
    function theme_asset($path){
		$theme = get_option('active_theme','default');

		return asset("public/theme/$theme/$path");		
    }
}

if ( ! function_exists('xss_clean')){
	function xss_clean($data)
	{
		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

		do
		{
		    // Remove really unwanted tags
		    $old_data = $data;
		    $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);

		// we are done...
		return $data;
	}
}


/** Create Activity Log **/
if ( ! function_exists('create_log')){
    function create_log($related_to, $related_id, $activity){
    	$log = new \App\ActivityLog();
    	$log->related_to = $related_to;
    	$log->related_id = $related_id;
    	$log->activity = $activity;
    	$log->user_id = Auth::id();
    	$log->company_id = company_id();
    	$log->save();
    }
}


// convert seconds into time
if ( ! function_exists('time_from_seconds')){
	function time_from_seconds($seconds) { 
	    $h = floor($seconds / 3600); 
	    $m = floor(($seconds % 3600) / 60); 
	    $s = $seconds - ($h * 3600) - ($m * 60); 
	    return sprintf('%02d:%02d:%02d', $h, $m, $s); 
	} 
}


if ( ! function_exists('get_language')){
	function get_language() { 
	    $user_language = session('user_language'); 
		
		if($user_language == ''){	
		    if(Auth::check()){
				$user_language = Auth::user()->language == '' ? get_option('language') : Auth::user()->language;
				session(['user_language' => $user_language]);
			}else{
				$user_language = get_option('language');
				session(['user_language' => $user_language]);
			}
		}
		
		return $user_language;
	} 
}

if ( ! function_exists('checkRoute')){
	function checkRoute($route) {
		$notAllowed = [
			url('company'),
			url('administration'),
			url('profile'),
			url('membership'),
		];
		
		if (in_array(url($route), $notAllowed)){
			return false;
		}
		return true;
		
	}
}

