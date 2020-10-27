<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'current_stocks';
	
	protected $guarded = [];  

    public function product()
    {
        return $this->belongsTo('App\Product',"product_id")->withDefault();
    }

}