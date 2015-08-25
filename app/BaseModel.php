<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Optimus\Optimus;
use Watson\Rememberable\Rememberable;

abstract class BaseModel extends Model
{
    use Rememberable;

    /**
     * {@inheritDoc}
     */
    public function find($id, $columns = ['*'])
    {
        $transformedId = app(Optimus::class)->decode($id);

        return static::query()->find($transformedId, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $transformedId = app(Optimus::class)->decode($id);

        return static::query()->findOrFail($transformedId, $columns);
    }

    /**
     * Accessor for id
     *
     * @param  int $value
     * @return int
     */
    public function getIdAttribute($value)
    {
        return app(Optimus::class)->encode($value);
    }
}