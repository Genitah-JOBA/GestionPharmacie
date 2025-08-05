<?php

namespace App\Events;

use App\Models\Medicament;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicamentUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $medicament;

    public function __construct(Medicament $medicament)
    {
        $this->medicament = $medicament;
    }

    public function broadcastOn()
    {
        // canal public pour commencer
        return new Channel('medicaments');
    }

    public function broadcastAs()
    {
        return 'MedicamentUpdated';
    }

    public function broadcastWith()
    {
        return [
            'reference' => $this->medicament->reference,
            'nom' => $this->medicament->nom,
            'quantite' => $this->medicament->quantite,
            'date_expiration' => $this->medicament->date_expiration,
            'frequence_utilisation' => $this->medicament->frequence_utilisation,
        ];
    }
}
