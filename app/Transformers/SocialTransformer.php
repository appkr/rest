<?php

namespace App\Transformers;

use App\Social;
use League\Fractal;

class SocialTransformer extends Fractal\TransformerAbstract
{
    /**
     * Transform single resource
     *
     * @param Social $social
     *
     * @return array
     */
    public function transform(Social $social)
    {
        return [
            'provider'    => $social->provider,
            'provider_id' => $social->provider_id,
            'name'        => $social->name,
            'nickname'    => $social->nickname,
            'avatar'      => $social->avatar,
            'email'       => $social->email,
            'created_at'  => (int) $social->created_at->getTimestamp()
        ];
    }
}