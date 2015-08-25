<?php

namespace App\Transformers;

use App\Profile;
use League\Fractal;

class ProfileTransformer extends Fractal\TransformerAbstract
{
    /**
     * Transform single resource
     *
     * @param Profile $profile
     *
     * @return array
     */
    public function transform(Profile $profile)
    {
        return [
            'bio'     => $profile->bio,
            'slack'   => $profile->slack,
            'hipchat' => $profile->hipchat
        ];
    }
}