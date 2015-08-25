<?php

use Bican\Roles\Models\Role;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    use DatabaseHelper;

    public function run()
    {
        $faker = $this->beginSeeding(new App\User);

        $roles = Role::lists('id')->toArray();

        $me = factory(App\User::class, 'me')->create();
        $me->attachRole(Role::whereSlug('root')->first());

        $users = factory(App\User::class, 5)
            ->create()
            ->each(function($user) use($faker, $roles) {
                $user->attachRole($faker->randomElement($roles));
            });

        $this->endSeeding();
    }
}
