<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quotations';
    
	public function quotation_items()
    {
        return $this->hasMany('App\QuotationItem',"quotation_id");
    }

    public function client()
    {
        return $this->belongsTo('App\Contact',"related_id")->withDefault();
    }

    public function lead()
    {
        return $this->belongsTo('App\Lead',"related_id")->withDefault();
    }


}