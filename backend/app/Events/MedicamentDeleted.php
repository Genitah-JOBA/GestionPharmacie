<?php

namespace App\Events;

use App\Models\Medicament;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicamentDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $medicament;

    /**
     * Create a new event instance.
     */
    public function __construct(Medicament $medicament)
    {
        $this->medicament = $medicament;
    }

    /**
     * The name of the channel to broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('medicaments');
    }

    /**
     * Optionnel : nom de l’événement côté front
     */
    public function broadcastAs()
    {
        return 'medicament.deleted';
    }

    /**
     * Données envoyées au client
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->medicament->id,
            'reference' => $this->medicament->reference,
            'nom' => $this->medicament->nom,
        ];
    }
}
