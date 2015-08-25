<?php

use App\Todo;
use App\User;
use Illuminate\Database\Seeder;

class TodosTableSeeder extends Seeder
{
    use DatabaseHelper;

    public function run()
    {
        $faker = $this->beginSeeding(new Todo);

        $userIds = User::lists('id')->toArray();

        foreach (range(1, 100) as $index) {
            Todo::create([
                'title'       => $faker->sentence(),
                'user_id'     => $faker->randomElement($userIds),
                'description' => $faker->randomElement([$faker->paragraph(), null]),
                'done'        => $faker->randomElement([0, 1])
            ]);
        }

        $this->endSeeding();
    }
}
