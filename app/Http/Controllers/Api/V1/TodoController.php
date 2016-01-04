<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TodoRequest;
use App\Todo;
use App\Transformers\TodoTransformer;
use Appkr\Api\Http\Response;
use Illuminate\Http\Request;

/**
 * Class TodoController
 *
 * @package App\Http\Controllers
 */
class TodoController extends Controller
{
    /**
     * @var \App\Todo
     */
    protected $model;

    /**
     * @var \Appkr\Fractal\Response
     */
    protected $response;

    /**
     * @param \App\Todo               $repo
     * @param \Appkr\Api\Http\Response $respond
     */
    public function __construct(Todo $repo, Response $respond)
    {
        $this->middleware('jwt.auth', ['except' => ['index', 'show']]);
        $this->middleware('role:member', ['except' => ['index', 'show']]);
        $this->middleware('throttle:10,1');

        $this->model   = $repo;
        $this->respond = $respond;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Appkr\Fractal\Response
     */
    public function index()
    {
        return $this->respond->withPagination(
            $this->model->with('user')->latest()->paginate(25),
            new TodoTransformer
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\TodoRequest $request
     * @return \Appkr\Fractal\Response
     */
    public function store(TodoRequest $request)
    {
        if (! $todo = $request->user()->todos()->create($request->all())) {
            return $this->respond->internalError('Failed to create new Todo.');
        }

        return $this->respond->setStatusCode(201)->setMeta([
            'todo' => $this->respond->getItem($todo, new TodoTransformer),
        ])->success(strip_tags(trans('messages.created')));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Appkr\Fractal\Response
     */
    public function show($id)
    {
        return $this->respond->withItem(
            $this->model->findOrFail($id),
            new TodoTransformer
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\TodoRequest $request
     * @param int                            $id
     * @return \Appkr\Fractal\Response
     */
    public function update(TodoRequest $request, $id)
    {
        if (! $this->model->whereId($id)->whereUserId($request->user()->id)->exists()) {
            return $this->respond->unauthorizedError();
        }

        $todo = $this->model->findOrFail($id);

        if (! $todo->update($request->all())) {
            return $this->respond->internalError();
        }

        return $this->respond->Success(strip_tags(trans('messages.updated')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @return \Appkr\Fractal\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! $this->model->whereId($id)->whereUserId($request->user()->id)->exists()) {
            return $this->respond->unauthorizedError();
        }

        $todo = $this->model->findOrFail($id);

        if (! $todo->delete()) {
            return $this->respond->internalError();
        }

        return $this->respond->success(strip_tags(trans('messages.deleted')));
    }
}
