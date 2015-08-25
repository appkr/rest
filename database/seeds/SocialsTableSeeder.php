<?php

use Illuminate\Database\Seeder;

class SocialsTableSeeder extends Seeder
{
    use DatabaseHelper;

    public function run()
    {
        $faker = $this->beginSeeding(new App\Social);

        $me = factory(\App\Social::class, 'me')->create();

        $users = App\User::where('email', '!=', 'juwonkim@me.com')->get();

        foreach($users as $user) {
            $user->socials()->create([
                'provider'    => $faker->randomElement(config('settings.social') + [null]),
                'provider_id' => $faker->numberBetween(5000000, 9000000),
                'name'        => $faker->firstName . ' ' . $faker->lastName,
                'nickname'    => $faker->userName,
                'avatar'      => $faker->url,
                'email'       => $faker->safeEmail,
            ]);
        }

        $this->endSeeding();
    }
}
