<?php

namespace App\Notifications\Slack;

use Carbon\Carbon;
use Exception;
use InvalidArgumentException;
use Maknz\Slack\Attachment;
use Maknz\Slack\AttachmentField;
use Request;
use Route;

class ErrorReporting extends Slack
{
    /**
     * Build Slack Attachment payload
     *
     * @param Exception $e
     *
     * @return Attachment
     */
    protected function buildAttachment($e)
    {
        if (! $e instanceof Exception) {
            throw new InvalidArgumentException(
                sprintf("Expecting instance of \\Exception, %s given.", gettype($e))
            );
        }

        $eClass   = get_class($e);
        $username = auth()->check() ? auth()->user()->username : 'Unknown';
        $email    = auth()->check() ? auth()->user()->email : 'no-such-user';
        $route    = Route::getCurrentRoute() ?: 'not-existing-route';

        return new Attachment([
            'fallback' => null,
            'text'     => "{$e->getMessage()} {$eClass}: {$e->getLine()}",
            'fields'   => [
                new AttachmentField([
                    'title' => 'Time',
                    'value' => Carbon::now('Asia/Seoul')->toDateTimeString(),
                    'short' => true
                ]),
                new AttachmentField([
                    'title' => 'User',
                    'value' => "<" . 'http:' . get_profile_url($email) . "|{$username}>",
                    'short' => true
                ]),
                new AttachmentField([
                    'title' => 'Route',
                    'value' => $route,
                    'short' => true
                ]),
                new AttachmentField([
                    'title' => 'Class',
                    'value' => "{$eClass}: {$e->getLine()}"
                ]),
                new AttachmentField([
                    'title' => 'File',
                    'value' => $e->getFile()
                ]),
                new AttachmentField([
                    'title' => 'Message',
                    'value' => $e->getMessage()
                ]),
                new AttachmentField([
                    'title' => 'Remote IP',
                    'value' => Request::ip()
                ]),
                new AttachmentField([
                    'title' => 'Request Url',
                    'value' => sprintf("%s %s", Request::method(), Request::fullUrl())
                ]),
                new AttachmentField([
                    'title' => 'Request Headers',
                    'value' => Request::header()
                ]),
                new AttachmentField([
                    'title' => 'Request Content',
                    'value' => Request::instance()->getContent() ?: json_encode(Request::all())
                ])
            ]
        ]);
    }
}
