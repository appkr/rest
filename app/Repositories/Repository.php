<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Repository
 */
abstract class Repository
{
    /**
     * @var
     */
    protected $model;

    /**
     * Repository
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public abstract function model();

    /**
     * Setter for model property
     */
    protected function setModel()
    {
        $model = app($this->model());

        if (! $model instanceof Model) {
            throw new \Exception(
                "Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }

        $this->model = app($this->model());
    }
}