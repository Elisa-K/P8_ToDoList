<?php

namespace App\Handlers\TaskHandlers;

use App\Entity\Task;
use App\Form\TaskType;
use App\Handlers\HandlerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class TaskEditHandler extends HandlerManager
{

    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(Task $task, Request $request): bool
    {
        if ($this->handleForm(TaskType::class, $task, $request)) {
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}