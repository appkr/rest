<?php

use Illuminate\Database\Seeder;

class ProfilesTableSeeder extends Seeder
{
    use DatabaseHelper;

    public function run()
    {
        $faker = $this->beginSeeding(new App\Profile);

        $me = factory(\App\Profile::class, 'me')->create();

        $users = App\User::where('email', '!=', 'juwonkim@me.com')->get();

        foreach($users as $user) {
            $user->profile()->create([
                'bio'     => $faker->paragraph(),
                'slack'   => $faker->userName,
                'hipchat' => $faker->userName,
            ]);
        }

        $this->endSeeding();
    }
}
