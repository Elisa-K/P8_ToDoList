<?php

namespace App\Handlers\TaskHandlers;

use App\Entity\Task;
use App\Form\TaskType;
use App\Handlers\HandlerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Service\Attribute\Required;

class TaskEditHandler
{

    private EntityManagerInterface $entityManager;

    private HandlerManager $handlerManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Required]
    public function setHandlerManager(HandlerManager $handlerManager): void
    {
        $this->handlerManager = $handlerManager;
    }


    public function handle(Task $task, Request $request): bool
    {
        if ($this->handlerManager->handleForm(TaskType::class, $task, $request)) {
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function getForm(): FormInterface
    {
        return $this->handlerManager->getForm();
    }
}