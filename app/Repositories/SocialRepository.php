<?php

namespace App\Repositories;

use App\Social;
use App\Contracts\SocialRedirectContract;
use App\User;

class SocialRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return \App\Social::class;
    }

    /**
     * Find user by his/her name
     *
     * @param $nickname
     *
     * @return mixed
     */
    public function findByNickname($nickname)
    {
        return $this->model->whereName($nickname)->first();
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
        return $this->model->whereEmail($email)->first();
    }

    /**
     * Create a social entry and user if not exists
     *
     * @param array                  $socialData
     * @param SocialRedirectContract $listener
     *
     * @return mixed
     */
    public function firstOrCreate(array $socialData, SocialRedirectContract $listener)
    {
        $account = $this->model
            ->whereEmail($socialData['email'])
            ->whereProvider($socialData['provider'])
            ->first();

        if ($account) {
            // update social account info and swap it with the existing user
            $user = $this->updateAccountInfo($account, $socialData);

            flash(trans('auth.successLogin', ['name' => $account->user->name]));
        } else {
            // create new user if not exists
            $user = User::whereEmail($socialData['email'])->first();
            $user = $this->createUserIfNotExist($user, $socialData);

            // crate new social account from the user
            $account = $user->socials()->create($socialData);
        }

        return $listener->onSocialLoginSuccess($user);
    }

    /**
     * Activate account if the given $user exists,
     * otherwise create a $user and activate
     *
     * @param User|null $user
     * @param array     $socialData
     *
     * @return $this|static
     */
    protected function createUserIfNotExist($user, array $socialData)
    {
        if (! $user) {
            $user = User::create([
                'name'  => $socialData['name'],
                'email' => $socialData['email'],
            ]);

            event('NewUserRegistered', [$user]);
            event('UserActivated', [$user]);
            flash(trans('auth.activated'));
        } else {
            flash(trans('auth.successLogin', ['name' => $user->name]));
        }

        return $user;
    }

    /**
     * Update user profile
     *
     * @param Social $oldAccount
     * @param array  $newAccount
     *
     * @return bool|int
     */
    private function updateAccountInfo(Social $oldAccount, array $newAccount)
    {
        $user = $oldAccount->user;

        if ($user && $user->activated != 1) {
            event('UserActivated', [$user]);
        }

        $oldAccount->fill($newAccount)->save();

        return $user->fresh();
    }
}