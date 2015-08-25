<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ClearCache extends Event
{
    use SerializesModels;

    /**
     * @var array
     */
    public $tags;

    /**
     * Create a new event instance.
     *
     * @param $tags
     */
    public function __construct($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
