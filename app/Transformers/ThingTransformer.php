<?php
namespace App\Transformers;

use App\Thing;
use League\Fractal;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class ThingTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include using url query string.
     * e.g. collection case -> ?include=comments:limit(5|1):order(created_at|desc)
     *      item case       -> ?include=author
     *
     * @var  array
     */
    protected $availableIncludes = ['author', 'comments'];

    /**
     * List of resources to include automatically/always.
     *
     * @var  array
     */
    // protected $defaultIncludes = ['author', 'comments'];
    /**
     * Transform single resource.
     *
     * @param  \App\Thing $thing
     * @return  array
     */
    public function transform(Thing $thing)
    {
        return [
            'id' => (int) $thing->id,
            // ...
            'created' => $thing->created_at->toIso8601String(),
            'link' => [
                 'rel' => 'self',
                 'href' => route('api.v1.things.show', $thing->id),
            ],
        ];
    }

      /**
     * Include author.
     *
     * @param  \App\Thing $thing
     * @return  \League\Fractal\Resource\Item
     */
    public function includeAuthor(Thing $thing)
    {
        return $this->item($thing->author, new \App\Transformers\UserTransformer);
    }        /**
     * Include comments.
     *
     * @param  \App\Thing $thing
     * @param  \League\Fractal\ParamBag|null $params
     * @return  \League\Fractal\Resource\Collection
     * @throws  \Exception
     */
    public function includeComments(Thing $thing, $params)
    {
        if ($params) {
            $this->validateParams($params);
        }

        list($limit, $offset) = $params->get('limit') ?: config('api.include.defaultLimit');
        list($orderCol, $orderBy) = $params->get('order') ?: config('api.include.defaultOrder');

        $comments = $thing->comments()->take($limit)->offset($offset)->orderBy($orderCol, $orderBy)->get();

        return $this->collection($comments, new \App\Transformers\CommentTransformer);
    }  }
