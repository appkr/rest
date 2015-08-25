<?php

namespace App;

class Social extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'socials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'name',
        'nickname',
        'email',
        'avatar'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at'
    ];

    # Mutators

    /**
     * Set the (oauth) provider value
     *
     * @param string|null $value
     */
    public function setProviderAttribute($value = null)
    {
        $this->attributes['provider'] = $value ?: 'captive';
    }

    # Relationships

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
