<?php

namespace App\Contracts;

/**
 * Interface SocialAccountContract
 */
interface SocialAccountContract
{
    /**
     * Get social login provider name
     *
     * @return mixed
     */
    public function getSocialProviders();

    /**
     * Get user id obtained from social login provider
     *
     * @return mixed
     */
    public function getSocialIds();

    /**
     * Get nickname obtained from social login provider
     *
     * @return mixed
     */
    public function getNicknames();

    /**
     * Check the current user is registered with an social login provider.
     * Return true if he/she is, otherwise false.
     *
     * @return bool
     */
    public function isSocialUser();
}