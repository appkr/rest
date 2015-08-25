<?php

namespace App;

class Todo extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'todos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'user_id',
        'description',
        'done'
    ];

    # Relationships

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
