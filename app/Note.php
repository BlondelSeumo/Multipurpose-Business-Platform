<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notes';
	
	public function user(){
    	return $this->belongsTo('App\User','user_id')->withDefault();
    }
}