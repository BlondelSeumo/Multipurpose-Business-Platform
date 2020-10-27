<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff_roles';
	
	public function permissions(){
		return $this->hasMany('App\AccessControl','role_id');
	}
}