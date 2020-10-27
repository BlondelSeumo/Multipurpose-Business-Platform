<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_return_items';

    public function item()
    {
        return $this->belongsTo('App\Item',"product_id")->withDefault();
    }
}