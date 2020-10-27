<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoices';
    
	public function invoice_items()
    {
        return $this->hasMany('App\InvoiceItem',"invoice_id");
    }

    public function client()
    {
        return $this->belongsTo('App\Contact',"client_id")->withDefault();
    }

    public function project()
    {
        return $this->belongsTo('App\Project',"related_id")->withDefault();
    }

    public function company()
    {
        return $this->belongsTo('App\Company',"company_id")->withDefault();
    }

}