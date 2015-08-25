<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class TodoControllerTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseTransactions;

    /**
     * Stubbed User model
     *
     * @var /App/User
     */
    protected $user;

    /**
     * Stubbed Todo model
     *
     * @var array
     */
    protected $todo = [];

    /**
     * JWT token
     *
     * @var string
     */
    protected $jwtToken = 'header.payload.signature';

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->stubs();
    }

    /** @test */
    public function itFetchesACollectionOfTodos()
    {
        $this->get(route('api.v1.todo.index'), $this->getHeaders())
            ->seeStatusCode(200)
            ->seeJson();
    }

    /** @test */
    public function itFetchesASingleTodo()
    {
        $this->get(route('api.v1.todo.show', $this->todo['id']), $this->getHeaders())
            ->seeStatusCode(200)
            ->seeJson();
    }

    /** @test */
    public function itResponds404IfATodoIsNotFound()
    {
        $this->get(route('api.v1.todo.show', 10000), $this->getHeaders())
            ->seeStatusCode(404)
            ->seeJson();
    }

    /** @test */
    public function itResponds422IfANewTodoRequestFailsValidation()
    {
        $payload = [
            'title'       => null,
            'user_id'     => null,
            'description' => 'n'
        ];

        $this->post(route('api.v1.todo.store'), $payload, $this->getHeaders())
            ->seeStatusCode(422)
            ->seeJson();
    }

    /** @test */
    public function itResponds201WithCreatedTodoAfterCreation()
    {
        $payload = [
            'title'       => 'new title',
            'description' => 'new description'
        ];

        $this->actingAs($this->user)
            ->post(route('api.v1.todo.store'), $payload, $this->getHeaders())
            ->seeInDatabase('todos', $payload)
            ->seeStatusCode(201)
            ->seeJsonContains(['title' => 'new title']);
    }

    /** @test */
    public function itIsNotUpdatableIfTheTodoIsNotOwnedByThisUser()
    {
        $unauthorized = factory(\App\User::class)->make();

        $this->actingAs($unauthorized)
            ->put(
                route('api.v1.todo.update', $this->todo['id']),
                ['title' => 'MODIFIED title', '_method' => 'PUT'],
                $this->getHeaders()
            )
            ->seeStatusCode(401)
            ->seeJson();
    }

    /** @test */
    public function itResponds200IfAUpdateRequestIsSucceed()
    {
        $this->actingAs($this->user)
            ->put(
                route('api.v1.todo.update', $this->todo['id']),
                ['title' => 'MODIFIED title', '_method' => 'PUT'],
                $this->getHeaders()
            )
            ->seeInDatabase('todos', ['title' => 'MODIFIED title'])
            ->seeStatusCode(200)
            ->seeJson();
    }

    /** @test */
    public function itResponds200IfADeleteRequestIsSucceed()
    {
        $this->actingAs($this->user)
            ->delete(
                route('api.v1.todo.destroy', $this->todo['id']),
                ['_method' => 'DELETE'],
                $this->getHeaders()
            )
            ->notSeeInDatabase('todos', ['id' => $this->todo['id']])
            ->seeStatusCode(200)
            ->seeJson();
    }

    /** @test */
    public function itIsNotDeletableIfTheTodoIsNotOwnedByThisUser()
    {
        $unauthorized = factory(\App\User::class)->make();

        $this->actingAs($unauthorized)
            ->delete(
                route('api.v1.todo.destroy', $this->todo['id']),
                ['_method' => 'DELETE'],
                $this->getHeaders()
            )
            ->seeStatusCode(401)
            ->seeJson();
    }

    /**
     * Stub
     */
    public function stubs()
    {
        $this->user = factory(\App\User::class)->create([
            'name' => 'foo'
        ]);

        $this->todo = factory(\App\Todo::class)->create([
            'title'       => 'title',
            'user_id'     => $this->user->id,
            'description' => 'description'
        ])->toArray();
    }

    /**
     * Set/Get http request header
     *
     * @param array $append
     *
     * @return array
     */
    protected function getHeaders($append = [])
    {
        return [
            'Authorization' => "Bearer {$this->jwtToken}",
            'Accept'        => 'application/json'
        ] + $append;
    }
}