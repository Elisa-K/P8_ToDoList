<?php

namespace App\Handlers\TaskHandlers;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Handlers\HandlerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class TaskAddHandler extends HandlerManager
{
    private Security $security;

    public function __construct(Security $security, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager)
    {
        parent::__construct($formFactory, $entityManager);
        $this->security = $security;
    }

    public function handle(Task $task, Request $request): bool
    {
        if ($this->handleForm(TaskType::class, $task, $request)) {
            /** @var User $user */
            $user = $this->security->getUser();

            $task->setAuthor($user);

            $this->processAdd($task);

            return true;
        }

        return false;
    }
}
