<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class EmailChanged
{
    use Dispatchable, SerializesModels;

    public $user;
    public $oldEmail;
    public $newEmail;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $oldEmail, $newEmail)
    {
        $this->user = $user;
        $this->oldEmail = $oldEmail;
        $this->newEmail = $newEmail;
    }
}
