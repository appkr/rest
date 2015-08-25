<?php

namespace App\Notifications\Slack;

use Maknz\Slack\Client;

abstract class Slack
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Slack.
     *
     * @param null  $endpoint
     * @param array $settings
     */
    public function __construct($endpoint = null, $settings = [])
    {
        ;
        $this->client = new Client(
            $endpoint ?: config('slack.endpoint'),
            $settings
        );
    }

    /**
     * Build Slack Attachment payload
     *
     * @param $data
     *
     * @return \Maknz\Slack\Attachment|array
     */
    protected function buildAttachment($data)
    {
        throw new \BadMethodCallException(sprintf("You should implement your own %s method.", __METHOD__));
    }

    /**
     * Send a Slack message.
     *
     * @param string $payload
     */
    public function send($payload)
    {
        $message = $this->client->createMessage();

        if (! is_string($payload)) {
            $text = null;

            if (is_array($payload)) {
                $text = $payload['message'];
            } elseif ($payload instanceof \Exception) {
                $text = $payload->getMessage();
            }

            return $message->attach($this->buildAttachment($payload))
                ->setText($text)->send();
        }

        return $message->setText($payload)->send();
    }
}