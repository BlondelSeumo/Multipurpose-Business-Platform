<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'project_members';

    public function user()
    {
        return $this->belongsTo('App\User',"user_id")->withDefault();
    }
}