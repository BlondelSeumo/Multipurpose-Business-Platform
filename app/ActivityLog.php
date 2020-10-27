<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'activity_logs';

    public function created_by()
    {
        return $this->belongsTo('App\User','user_id')->withDefault();
    }
	
	public function getCreatedAtAttribute($date)
	{
		$date_format = get_company_option('date_format','Y-m-d');	
		$time_format = get_company_option('time_format',24) == '24' ? 'H:i' : 'h:i A';
		
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format("$date_format $time_format");
	}
}