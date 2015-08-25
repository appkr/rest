<?php

namespace App;

class Profile extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'bio',
        'slack',
        'hipchat',
        'noti_email',
        'noti_slack',
        'noti_hipchat'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at'
    ];

    # Relationships

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
