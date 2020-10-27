<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';
	
	public function package()
    {
        return $this->belongsTo('App\Package',"package_id")->withDefault();
    }
}