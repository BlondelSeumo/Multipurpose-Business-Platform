<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leads';

    public function lead_status()
    {
        return $this->belongsTo('App\LeadStatus','lead_status_id')->withDefault();
    }

    public function lead_source()
    {
        return $this->belongsTo('App\LeadSource','lead_source_id')->withDefault();
    }

    public function assigned_user()
    {
        return $this->belongsTo('App\User','assigned_user_id')->withDefault();
    }

}