<?php

use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Model as EloquentModel;

trait DatabaseHelper
{
    /**
     * Prepare for a database seeding
     *
     * @param EloquentModel $model
     * @param bool          $faker
     *
     * @return \Faker\Generator
     */
    public function beginSeeding(EloquentModel $model = null, $faker = true)
    {
        if (app()->environment() !== 'testing') {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        if ($model) {
            $model->truncate();
        }

        return $faker
            ? Faker::create()
            : null;
    }

    /**
     * Finish the seeding
     */
    public function endSeeding()
    {
        if (app()->environment() !== 'testing') {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return;
    }

    /**
     * Determine if the framework is 5.1 or higher
     *
     * @return bool
     */
    public function isL51()
    {
        return substr($this->app->version(), 0, 3) >= 5.1;
    }
}