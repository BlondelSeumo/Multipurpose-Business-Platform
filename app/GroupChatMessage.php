<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupChatMessage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'group_chat_messages';
	
	public function sender(){
		return $this->belongsTo('App\User','sender_id')->withDefault();
	}
	
	public function group(){
		return $this->belongsTo('App\ChatGroup','group_id')->withDefault();
	}
	
}