<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    public function project(){
    	return $this->belongsTo('App\Project','project_id')->withdefault();
    }

    public function milestone(){
        return $this->belongsTo('App\ProjectMilestone','milestone_id')->withdefault();
    }

    public function status(){
    	return $this->belongsTo('App\TaskStatus','task_status_id')->withdefault();
    }

    public function assigned_user(){
    	return $this->belongsTo('App\User','assigned_user_id')->withdefault();
    }
}