<?php

namespace App\Handlers\TaskHandlers;

use App\Entity\Task;
use App\Form\TaskType;
use App\Handlers\HandlerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class TaskEditHandler extends HandlerManager
{
    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager)
    {
        parent::__construct($formFactory, $entityManager);
    }

    public function handle(Task $task, Request $request): bool
    {
        if ($this->handleForm(TaskType::class, $task, $request)) {
            $this->processUpdate();

            return true;
        }

        return false;
    }
}
