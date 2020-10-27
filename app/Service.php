<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'services';

	protected $guarded = []; 
	
    public function tax()
    {
        return $this->belongsTo('App\Tax',"tax_id")->withDefault();
    }
}