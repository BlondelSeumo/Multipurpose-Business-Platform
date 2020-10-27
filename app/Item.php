<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'items';
	
	protected $guarded = [];  

    public function product()
    {
        return $this->hasOne('App\Product',"item_id")->withDefault();
    }
	
	
	public function service()
    {
        return $this->hasOne('App\Service',"item_id")->withDefault();
    }
	
	
	public function product_stock()
    {
        return $this->hasOne('App\Stock',"product_id");
    }

}