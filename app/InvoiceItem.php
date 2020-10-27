<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_items';

    public function item()
    {
        return $this->belongsTo('App\Item',"item_id")->withDefault();
    }

}