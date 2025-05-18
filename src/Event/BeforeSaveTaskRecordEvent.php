<?php

namespace GrowthTaskBundle\Event;

use GrowthTaskBundle\Entity\Task;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeSaveTaskRecordEvent extends Event
{
    private UserInterface $user;

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    private array $result = [];

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    private bool $return = true;

    private Task $task;

    public function isReturn(): bool
    {
        return $this->return;
    }

    public function setReturn(bool $return): void
    {
        $this->return = $return;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): void
    {
        $this->task = $task;
    }
}
