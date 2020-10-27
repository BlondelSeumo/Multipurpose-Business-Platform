<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSubscriber extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cm_email_subscribers';
}