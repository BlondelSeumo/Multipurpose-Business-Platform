<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_groups';
	
	public function creator(){
		return $this->belongsTo('App\User','created_by')->withDefault();
	}
	
	public function group_members(){
		return $this->belongsToMany('App\User', 'chat_group_users', 'group_id', 'user_id');
	}
	
}