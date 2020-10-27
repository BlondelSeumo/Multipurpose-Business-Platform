<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeSheet extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'timesheets';


    public function user(){
    	return $this->belongsTo('App\User','user_id')->withDefault();
    }

    public function task(){
    	return $this->belongsTo('App\Task','task_id')->withDefault();
    }

}