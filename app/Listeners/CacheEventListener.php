<?php

namespace App\Listeners;

use App\Events\ClearCache;
use Illuminate\Cache\Repository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheListener
{
    /**
     * @var Store
     */
    private $cache;

    /**
     * Create the event listener.
     *
     * @param Repository $cache
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the event.
     *
     * @param  ClearCache  $event
     * @return void
     */
    public function handle(ClearCache $event)
    {
        if (! is_array($event->tags)) {
            return $this->cache->tags($event->tags)->flush();
        }

        foreach ($event->tags as $tag) {
            $this->cache->tags($tag)->flush();
        }

        return;
    }
}
