<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\DBNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
        'name', 'email', 'password', 'user_type', 'status', 'profile_picture',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
	public function client(){
		return $this->hasMany('App\Contact','user_id');
	}
	
	public function company(){
		return $this->belongsTo('App\Company','company_id')->withDefault();
	}

    public function role(){
        return $this->belongsTo('App\Role','role_id')->withDefault();
    }
	
	public function chat_groups(){
		return $this->belongsToMany('App\ChatGroup', 'chat_group_users', 'user_id', 'group_id');
	}

    public function projects(){
        return $this->belongsToMany('App\Project','project_members', 'user_id', 'project_id')->orderBy('id','desc');
    }

    public function tasks(){
        return $this->hasMany('App\Task', 'assigned_user_id')->orderBy('id','desc');
    }
	
	public function notifications()
    {
        return $this->morphMany(DBNotification::class, 'notifiable')
                            ->orderBy('created_at', 'desc');
    }

}
