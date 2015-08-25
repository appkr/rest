<?php

namespace App\Repositories;

use Bican\Roles\Models\Role;
use App\Contracts\SocialRedirectContract;
use App\User;

class UserRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return \App\User::class;
    }

    /**
     * Find user by his/her name
     *
     * @param $username
     *
     * @return mixed
     */
    public function findByUsername($username)
    {
        return $this->model->whereName($username)->first();
    }

    /**
     * Find user by his/her email
     *
     * @param $email
     *
     * @return mixed
     */
    public function findByEmail($email)
    {
        return $this->model->whereEmail($email);
    }

    /**
     * Find user by his/her activation code
     *
     * @param $code
     *
     * @return mixed
     */
    public function findByActivationCode($code)
    {
        return $this->model->whereActivated(0)->whereActivationCode($code)->firstOrFail();
    }

    /**
     * Create a user if not exists
     *
     * @param array $userData
     * @param       $listener
     *
     * @return mixed
     * @throws \Exception
     */
    public function firstOrCreate(array $userData, SocialRedirectContract $listener)
    {
        $user = $this->model->whereEmail($userData['email'])->first();

        if ($user) {
            $this->updateUserInfo($user, $userData);

            if ($user->activated != 1) {
                event('UserActivated', [$user]);
            }

            flash(trans('auth.successLogin', ['name' => $user->name]));
        } else {
            $user = $this->model->create($userData);

            // Grant default Role to this User
            $defaultRole = Role::whereSlug(config('acl.defaultRole'))->first();
            $user->attachRole($defaultRole);
            $user = $user->fresh();

            event('UserActivated', [$user]);
            flash(trans('auth.activated'));
        }

        return $listener->onSocialLoginSuccess($user);
    }

    /**
     * Update user profile
     *
     * @param User  $oldUser
     * @param array $newUser
     *
     * @return bool|int
     */
    private function updateUserInfo(User $oldUser, array $newUser)
    {
        return $oldUser->fill($newUser)->save();
    }

    /**
     * Get the list of associative array consist of ['id' => 'name']
     *
     * @param $column
     * @param $search
     *
     * @return mixed
     */
    public function suggest($column, $search)
    {
        return $this->model->where($column, 'like', '%' . $search . '%')
            ->all(['id', \DB::raw("{$column} as name")]);
    }

    /**
     * Create model
     *
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return $this->model->create($data);
    }

    /**
     * Delete model from the database
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        return $this->model->findOrFail($id)->delete();
    }
}