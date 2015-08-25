<?php

namespace App\Transformers;

use League\Fractal;
use App\User;

class UserTransformer extends Fractal\TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'profile',
        'social'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'profile',
        'social'
    ];

    /**
     * Transform single resource
     *
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id'         => (int) $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'created_at' => (int) $user->created_at->getTimestamp(),
        ];
    }


    /**
     * Include Profile
     *
     * @param User $user
     *
     * @return Fractal\Resource\Item
     */
    public function includeProfile(User $user)
    {
        $profile = $user->profile;

        return ($profile)
            ? $this->item($profile, new ProfileTransformer)
            : null;
    }

    /**
     * Include Social Account
     *
     * @param User $user
     *
     * @return Fractal\Resource\Item
     */
    public function includeSocial(User $user)
    {
        $socials = $user->socials;

        return ($socials)
            ? $this->collection($socials, new SocialTransformer)
            : null;
    }
}