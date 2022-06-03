<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\User;

class UserEvent
{
    public const REGISTER_ACTION = 'user.register.action';
    public const RESET_PASSWORD  = 'user.reset.action';

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

}