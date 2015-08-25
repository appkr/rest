<?php

namespace App\Contracts;

use App\User;

/**
 * Interface SocialRedirectContract
 */
interface SocialRedirectContract
{

    /**
     * Handle login
     *
     * @param User $user
     *
     * @return mixed
     */
    public function onSocialLoginSuccess(User $user);

}