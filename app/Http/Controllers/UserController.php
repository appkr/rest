<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $repo;

    /**
     * @param UserRepository $repo
     */
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Suggest list of users in JSON
     * e.g. ['id' => 1, 'name' => 'User Name']
     *
     * @param null $candidate
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function suggest($candidate = null)
    {
        if (stristr($candidate, '@')) {
            $candidate = trim($candidate, '@');
        }

        $users = $this->repo->suggest('name', $candidate);

        if ($users) {
            return response()->json(['data' => $users->toArray()]);
        }

        return;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = $this->repo->find($id);

        return view('');
    }

    /**
     * Display the specified resource.
     *
     * @param $username
     *
     * @return Response
     */
    public function profile($username)
    {
        $user = $this->repo->findByUsername(trim($username, '@'));

        return $this->show($user->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
