<?php

namespace App\Events;

use App\Models\MPFMTest;
use App\Models\MPFMTestValidation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MPFMTestValidated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public MPFMTest $test,
        public MPFMTestValidation $validation
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
