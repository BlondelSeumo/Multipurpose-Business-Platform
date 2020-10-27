<?php

namespace App\Http\Controllers\Install;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Artisan;

use App\Http\Controllers\Controller;
use App\Utilities\Installer;
use Validator;
use Hash;

class UpdateController extends Controller
{	
	public function __construct()
    {	
		
    }
	
	public function update_migration(){
		 Artisan::call('migrate', ['--force' => true]);
		 echo "Migration Updated Sucessfully";
	} 
}
