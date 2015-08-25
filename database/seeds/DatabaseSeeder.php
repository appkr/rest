<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment() === 'production') {
            exit('Man, this is production environment!');
        }

        Model::unguard();

        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(SocialsTableSeeder::class);
        $this->call(UserSettingsTableSeeder::class);
        $this->call(TodosTableSeeder::class);

        Model::reguard();
    }
}
