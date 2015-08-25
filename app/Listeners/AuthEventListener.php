<?php

namespace App\Listeners;

use App\Notifications\Mail\Mailer;
use App\Social;
use Bican\Roles\Models\Role;
use Illuminate\Events\Dispatcher;
use App\User;

class AuthEventListener
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Map events and handlers
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            'UserHasLoggedIn',
            static::class . '@onUserHasLoggedIn'
        );

        $events->listen(
            'UserHasLoggedOut',
            static::class . '@onUserHasLoggedOut'
        );

        $events->listen(
            'NewUserRegistered',
            static::class . '@onNewUserRegistered'
        );

        $events->listen(
            'UserActivated',
            static::class . '@onUserActivated'
        );

        $events->listen(
            'UserHasCancelled',
            static::class . '@onUserDeleted'
        );
    }

    /**
     * Handle user login event
     *
     * @param User $user
     *
     * @return bool
     */
    public function onUserHasLoggedIn(User $user)
    {
        // Updates last_login date on users table
        $now              = new \DateTime;
        $user->last_login = $now->format('Y-m-d H:i:s');

        return $user->save();
    }

    /**
     * Handle user logout event
     */
    public function onUserHasLoggedOut()
    {
        return;
    }

    /**
     * Handle user registration event
     *
     * @param User $user
     *
     * @return mixed
     */
    public function onNewUserRegistered(User $user)
    {
        // Grant default Role to this User
        $roles = $user->getRoles();
        if (! $roles) {
            $user->attachRole(Role::whereSlug(config('acl.defaultRole'))->first());
        }

        // Link user with social
        $socials = Social::whereEmail($user->email)->get();
        if ($socials) {
            foreach($socials as $social) {
                $social->user_id = $user->id;
                $social->save();
            }
        }

        // Send activation email
        if ($user->activated != 1) {
            $email   = $user->email;
            $subject = trans('email-subject.activation');
            $view    = 'emails.activation';
            $link    = route('user.activate', $user->activation_code);
            $data    = compact('subject', 'link');
            $cc      = [];
            $bcc     = [];

            $this->mailer->send($email, $subject, $view, $data, $cc, $bcc);
        }

        return true;
    }

    /**
     * Handle user's email confirmation event
     *
     * @param User $user
     *
     * @return mixed
     */
    public function onUserActivated(User $user)
    {
        if ($user->activated != 1) {
            // Update 'activated' attributes
            $now                   = new \DateTime;
            $user->activated_at    = $now->format('Y-m-d H:i:s');
            $user->activated       = 1;
            $user->activation_code = null;
            $user->save();

            // Promote user role to 'member'
            $user->attachRole(Role::whereSlug('member')->first());

            // Send welcome email
            $email   = $user->email;
            $subject = trans('email-subject.activated');
            $view    = 'emails.activated';
            $link    = route('session.create');
            $data    = compact('subject', 'link');
            $cc      = [];
            $bcc     = [];

            $this->mailer->send($email, $subject, $view, $data, $cc, $bcc);
        }

        return true;
    }

    /**
     * Handle user cancellation event
     *
     * @param User $user
     */
    public function onUserDeleted(User $user)
    {
        //TODO: send email saying he/she was removed.
    }
}