<?php

use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name'            => $faker->firstName . ' ' . $faker->lastName,
        'email'           => $faker->safeEmail,
        'password'        => $faker->randomElement([bcrypt('password'), null]),
        'remember_token'  => str_random(60),
        'activated'       => $activated = $faker->randomElement([0, 1]),
        'activation_code' => $activated ? null : str_random(60)
    ];
});

$factory->defineAs(App\User::class, 'me', function (Faker $faker) {
    return [
        'name'           => 'Juwon Kim',
        'email'          => 'juwonkim@me.com',
        'password'       => bcrypt('password'),
        'remember_token' => str_random(60),
        'activated'      => 1,
        'activated_at'   => (new DateTime)->format('Y-m-d H:i:s')
    ];
});

$factory->define(App\Social::class, function (Faker $faker) {
    return [
        'user_id'     => $faker->randomElement(App\User::lists('id')->toArray()),
        'provider'    => $faker->randomElement(config('settings.social') + ['captive', null]),
        'provider_id' => $faker->numberBetween(5000000, 9000000),
        'name'        => $faker->firstName . ' ' . $faker->lastName,
        'nickname'    => $faker->userName,
        'avatar'      => $faker->url,
        'email'       => $faker->safeEmail,
    ];
});

$factory->defineAs(App\Social::class, 'me', function (Faker $faker) {
    return [
        'user_id'     => \App\User::whereEmail('juwonkim@me.com')->first()->id ?: 1,
        'provider'    => 'github',
        'provider_id' => '6471947',
        'name'        => 'Appkr',
        'nickname'    => 'appkr',
        'avatar'      => 'https://avatars.githubusercontent.com/u/6471947?v=3',
        'email'       => 'juwonkim@me.com',
    ];
});

$factory->define(App\Profile::class, function (Faker $faker) {
    return [
        'user_id' => $faker->randomElement(App\User::lists(id)->toArray()),
        'bio'     => $faker->paragraph(),
        'slack'   => $faker->userName,
        'hipchat' => $faker->userName,
    ];
});

$factory->defineAs(App\Profile::class, 'me', function (Faker $faker) {
    return [
        'user_id' => \App\User::whereEmail('juwonkim@me.com')->first()->id ?: 1,
        'bio'     => 'Tech Manager, Web Developer, Tennis & Guitar Player',
        'slack'   => 'appkr',
        'hipchat' => 'appkr'
    ];
});

$factory->define(App\UserSetting::class, function (Faker $faker) {
    return [
        'user_id'      => $faker->randomElement(App\User::lists(id)->toArray()),
        'noti_email'   => $faker->randomElement([0, 1]),
        'noti_slack'   => $faker->randomElement([0, 1]),
        'noti_hipchat' => $faker->randomElement([0, 1])
    ];
});

$factory->define(App\Todo::class, function (Faker $faker) {
    return [
        'title'       => $faker->sentence,
        'user_id'     => $faker->randomElement(App\User::lists('id')->toArray()),
        'description' => $faker->randomElement([$faker->paragraph, null]),
        'done'        => $faker->randomElement([0, 1])
    ];
});

$factory->define(\Bican\Roles\Models\Role::class, function (Faker $faker) {
    $config = config('acl');

    return [
        'slug'        => $slug = $faker->randomElement(array_keys($config['roles'])),
        'name'        => ucfirst($slug),
        'description' => $faker->randomElement([$faker->paragraph, null]),
        'model'       => $faker->randomElement($config['allowedModels'])
    ];
});
