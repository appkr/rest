<?php

namespace App\Transformers;

use League\Fractal;
use App\Todo;

class TodoTransformer extends Fractal\TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'user'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'user'
    ];

    /**
     * Transform single resource
     *
     * @param Todo $todo
     *
     * @return array
     */
    public function transform(Todo $todo)
    {
        return [
            'id'          => (int) $todo->id,
            'title'       => $todo->title,
            'description' => $todo->description,
            'done'        => (bool) ($todo->done == 1) ? true : false,
            'created_at'  => (string) $todo->created_at
        ];
    }

    /**
     * Include User
     *
     * @param Todo $todo
     *
     * @return Fractal\Resource\Item
     */
    public function includeUser(Todo $todo)
    {
        $user = $todo->user;

        return ($user)
            ? $this->item($user, new UserTransformer)
            : null;
    }

}
