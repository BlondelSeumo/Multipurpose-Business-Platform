<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_histories';
	
	public function company()
    {
        return $this->belongsTo('App\Company')->withDefault();
    }

    public function package()
    {
        return $this->belongsTo('App\Package',"package_id")->withDefault();
    }
}