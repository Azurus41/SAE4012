<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameStateUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $state;
    public string $partyCode;

    public function __construct(string $partyCode, array $state)
    {
        $this->partyCode = $partyCode;
        $this->state = $state;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('game.' . $this->partyCode),
        ];
    }

    public function broadcastAs(): string
    {
        return 'state';
    }
}
