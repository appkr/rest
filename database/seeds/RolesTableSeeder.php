<?php

use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    use DatabaseHelper;

    public function run()
    {
        $faker = $this->beginSeeding(new Role);

        $permissions = Permission::lists('id')->toArray();
        $roles = config('acl.roles');

        foreach ($roles as $slug => $level) {
            $role = Role::create([
                'name'  => ucfirst($slug),
                'slug'  => $slug,
                'level' => $level
            ]);
            $role->attachPermission($faker->randomElement($permissions));
        }

        $this->endSeeding();
    }
}
