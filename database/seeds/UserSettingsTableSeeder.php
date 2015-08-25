<?php

use Illuminate\Database\Seeder;

class UserSettingsTableSeeder extends Seeder
{
    use DatabaseHelper;

    public function run()
    {
        $faker = $this->beginSeeding(new App\UserSetting);

        $users = App\User::get();

        foreach($users as $user) {
            $user->setting()->create([
                'noti_email'   => $faker->randomElement([0, 1]),
                'noti_slack'   => $faker->randomElement([0, 1]),
                'noti_hipchat' => $faker->randomElement([0, 1])
            ]);
        }

        $this->endSeeding();
    }
}
