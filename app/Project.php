<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

    public function client(){
    	return $this->belongsTo('App\Contact','client_id')->withDefault();
    }

    public function members(){
    	return $this->belongsToMany('App\User','project_members', 'project_id', 'user_id');
    }
}