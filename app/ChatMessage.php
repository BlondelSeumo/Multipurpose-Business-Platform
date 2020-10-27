<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_messages';
	
	public function sender(){
		return $this->belongsTo('App\User','from')->withDefault();
	}
	
	public function to(){
		return $this->belongsTo('App\User','to')->withDefault();
	}
}