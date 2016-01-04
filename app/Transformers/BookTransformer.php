<?php
namespace App\Transformers;

use App\Book;
use Appkr\Api\TransformerAbstract;
use League\Fractal;
use League\Fractal\ParamBag;

class BookTransformer extends TransformerAbstract
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
     * @param  \App\Book $book
     * @return  array
     */
    public function transform(Book $book)
    {
        return [
            'id' => (int) $book->id,
            // ...
            'created' => $book->created_at->toIso8601String(),
            'link' => [
                 'rel' => 'self',
                 'href' => route('api.v1.books.show', $book->id),
            ],
        ];
    }

      /**
     * Include author.
     *
     * @param  \App\Book $book
     * @return  \League\Fractal\Resource\Item
     */
    public function includeAuthor(Book $book)
    {
        return $this->item($book->author, new \App\Transformers\UserTransformer);
    }        /**
     * Include comments.
     *
     * @param  \App\Book $book
     * @param  \League\Fractal\ParamBag|null $params
     * @return  \League\Fractal\Resource\Collection
     * @throws  \Exception
     */
    public function includeComments(Book $book, $params)
    {
        if ($params) {
            $this->validateParams($params);
        }

        list($limit, $offset) = $params->get('limit') ?: config('api.include.limit');
        list($orderCol, $orderBy) = $params->get('order') ?: config('api.include.order');

        $comments = $book->comments()->limit($limit)->offset($offset)->orderBy($orderCol, $orderBy)->get();

        return $this->collection($comments, new \App\Transformers\CommentTransformer);
    }  }
