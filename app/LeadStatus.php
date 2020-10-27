<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lead_statuses';

    public function leads()
	{
	    return $this->hasMany('App\Lead','lead_status_id')->orderBy('id','desc');
	}
}