<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';
	
	public function account()
    {
        return $this->belongsTo('App\Account')->withDefault();
    }
	
	public function income_type()
    {
        return $this->belongsTo('App\ChartOfAccount',"chart_id")->withDefault();
    }
	
	public function payer()
    {
        return $this->belongsTo('App\Contact',"payer_payee_id")->withDefault();
    }
	
	public function expense_type()
    {
        return $this->belongsTo('App\ChartOfAccount',"chart_id")->withDefault();
    }
	
	public function payee()
    {
        return $this->belongsTo('App\Contact',"payer_payee_id")->withDefault();
    }
	
	public function payment_method()
    {
        return $this->belongsTo('App\PaymentMethod',"payment_method_id")->withDefault();
    }

    public function project()
    {
        return $this->belongsTo('App\Project',"project_id")->withDefault();
    }
	
}