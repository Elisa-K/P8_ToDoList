<?php

namespace App\Handlers\TaskHandlers;

use App\Entity\Task;
use App\Handlers\HandlerManager;
use Doctrine\ORM\EntityManagerInterface;

class TaskToggleHandler extends HandlerManager
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct(null, $entityManager);
    }

    public function handle(Task $task): bool
    {
        $task->toggle(!$task->isDone());
        $this->processUpdate();

        return $task->isDone();
    }
}
