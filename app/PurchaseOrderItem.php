<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_order_items';

    public function item()
    {
        return $this->belongsTo('App\Item',"product_id")->withDefault();
    }
}