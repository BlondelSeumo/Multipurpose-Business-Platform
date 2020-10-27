<?php

namespace App\Notifications;

use Illuminate\Notifications\DatabaseNotification;

class DBNotification extends DatabaseNotification
{

    public function user() {
        return $this->belongsTo('App\User','user_id')->withDefault();
    }

}