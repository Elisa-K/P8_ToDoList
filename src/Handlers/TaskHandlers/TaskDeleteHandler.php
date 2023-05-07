<?php

namespace App\Handlers\TaskHandlers;

use App\Entity\Task;
use App\Handlers\HandlerManager;
use Doctrine\ORM\EntityManagerInterface;

class TaskDeleteHandler extends HandlerManager
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct(null, $entityManager);
    }

    public function handle(Task $task): void
    {
        $this->processDelete($task);
    }
}
