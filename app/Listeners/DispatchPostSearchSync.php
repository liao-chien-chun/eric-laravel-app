<?php

namespace App\Listeners;

use App\Events\PostChanged;
use App\Jobs\SyncSinglePostToES;

class DispatchPostSearchSync
{
    /**
     * Handle the event.
     */
    public function handle(PostChanged $event): void
    {
        SyncSinglePostToES::dispatch($event->postId, $event->searchAction);
    }
}
