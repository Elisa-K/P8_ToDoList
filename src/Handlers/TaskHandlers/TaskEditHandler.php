<?php

namespace App\Handlers\TaskHandlers;

use Doctrine\ORM\EntityManagerInterface;

class TaskEditHandler
{
	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function handle(): void
	{
		$this->entityManager->flush();
	}
}