<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $postId,
        public string $searchAction = 'index'
    ) {}
}
