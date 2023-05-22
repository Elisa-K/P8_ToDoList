<?php

namespace App\Handlers\TaskHandlers;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Handlers\HandlerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class TaskAddHandler extends HandlerManager
{
    private Security $security;

    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function handle(Task $task, Request $request): bool
    {
        if ($this->handleForm(TaskType::class, $task, $request)) {
            /** @var User $user */
            $user = $this->security->getUser();

            $task->setAuthor($user);

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}