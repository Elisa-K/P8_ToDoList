<?php

namespace App\Handlers\TaskHandlers;

use App\Entity\Task;
use App\Handlers\HandlerManager;
use Doctrine\ORM\EntityManagerInterface;

class TaskDeleteHandler extends HandlerManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}