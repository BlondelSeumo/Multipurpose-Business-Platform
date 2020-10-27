<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessControl extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';
}