<?php

use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    use DatabaseHelper;

    public function run()
    {
        $this->beginSeeding(new Permission, false);

        $this->createPermissions();

        $this->endSeeding();
    }

    private function createPermissions()
    {

        $config = config('acl');

        $this->validateConfig($config['allowedModels']);
        $this->validateConfig($config['permissions']);

        // allowedModels comes first
        // because there should be (# of allowedModels x # of permissions)
        // ['Model1', 'Model2', ...]
        foreach ($config['allowedModels'] as $allowedModel) {

            // Loop through permissions
            // ['member' => ['create', 'edit', ...]]
            // Remember one Role can have more than one Permission
            foreach ($config['permissions'] as $roleSlug => $permissions) {
                $this->validateConfig($permissions);

                // Generate a single Permission
                // Here we create given permissions,
                // e.g. ['create', 'edit', ...]
                foreach ($permissions as $permission) {

                    $newPerm = Permission::create([
                        'name' => ucfirst($permission),
                        'slug' => $permission
                    ]);

                    // Attach generated Permission to each Role
                    // If there were n Permissions on a Role,
                    // There should be n entries of Permission on Role/Permission pivot
                    if ($role = Role::whereSlug($roleSlug)->first()) {
                        $role->attachPermission($newPerm);
                    }

                    // Set default allowed Model
                    // for so-called Entity Check
                    $newPerm->model = $allowedModel;
                    $newPerm->save();
                }
            }
        }
    }

    private function validateConfig($config)
    {
        if (! is_array($config)) {
            throw new Exception(sprintf("Array expected! %s given.", gettype($config)));
        }
    }

}
