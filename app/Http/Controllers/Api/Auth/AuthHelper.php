<?php

namespace App\Http\Controllers\Api\Auth;

use App\Transformers\UserTransformer;
use App\User;
use JWTAuth;

trait AuthHelper
{
    /**
     * Build User payload for response
     *
     * @param User $user
     *
     * @return array
     */
    public function buildUserMeta(User $user)
    {
        $config = config('settings.api');

        $acl = [
            'level' => $user->level(),
            'roles' => $user->getRoles()->lists('slug')->toArray()
        ];

        // Include ACL
        $meta = [
            'token' => $config['includeAcl']
                ? JWTAuth::fromUser($user, $acl)
                : JWTAuth::fromUser($user)
        ];

        // Include User
        if ($config['includeUser']) {
            $meta = array_merge($meta, [
                'user' => $this->response()->getItem($user, new UserTransformer),
                'acl'  => $acl
            ]);
        }

        return $meta;
    }
}