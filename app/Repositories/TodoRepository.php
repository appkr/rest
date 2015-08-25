<?php

namespace App\Repositories;

class TodoRepository extends Repository
{

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return \App\Todo::class;
    }

}