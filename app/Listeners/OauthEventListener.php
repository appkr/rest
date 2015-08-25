<?php

namespace App\Listeners;

use Illuminate\Events\Dispatcher;
use Log;

class OauthEventListener
{
    /**
     * Map events and handlers
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            'error.auth.client',
            static::class . '@onClientAuthFailed'
        );

        $events->listen(
            'error.auth.user',
            static::class . '@onUserAuthFailed'
        );

        $events->listen(
            'session.owner',
            static::class . '@onSessionHasAllocated'
        );
    }

    public function onClientAuthFailed($event)
    {
        Log::info($event->getRequest());
        // This event is emitted when a CLIENT fails to authenticate.
        // You might wish to listen to this event in order to ban clients
        // that fail to authenticate after n number of attempts.
    }

    public function onUserAuthFailed($event)
    {
        Log::info($event->getRequest());
        // This event is emitted when a USER fails to authenticate.
        // You might wish to listen to this event in order to reset passwords
        // or ban users that fail to authenticate after n number of attempts.
    }

    public function onSessionHasAllocated($event)
    {
        Log::info($event->getSession());
        // This event is emitted when a session has been allocated an owner
        // for example a user or a client).
        // You might want to use this event to dynamically associate scopes
        // to the session depending on the users role or ACL permissions.
    }
}