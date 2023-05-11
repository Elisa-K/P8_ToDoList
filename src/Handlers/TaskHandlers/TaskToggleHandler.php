<?php

namespace App\Handlers\TaskHandlers;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class TaskToggleHandler
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(Task $task): bool
    {
        $task->toggle(!$task->isDone());

        $this->entityManager->flush();

        return $task->isDone();
    }
}