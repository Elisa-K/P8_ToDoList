<?php

namespace App\Handlers\TaskHandlers;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TaskAddHandler
{
	private EntityManagerInterface $entityManager;

	private Security $security;

	public function __construct(EntityManagerInterface $entityManager, Security $security)
	{
		$this->entityManager = $entityManager;
		$this->security = $security;
	}

	public function handle(Task $task): void
	{
		/** @var User $user */
		$user = $this->security->getUser();

		$task->setAuthor($user);

		$this->entityManager->persist($task);
		$this->entityManager->flush();

	}
}