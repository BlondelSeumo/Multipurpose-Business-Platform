<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_statuses';

    public function tasks()
	{
	    return $this->hasMany('App\Task','task_status_id')->orderBy('id','desc');
	}
}