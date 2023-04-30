<?php

namespace App\Handlers\UserHandlers;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEditHandler
{
	private EntityManagerInterface $entityManager;

	private UserPasswordHasherInterface $userPasswordHasher;

	public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
	{
		$this->entityManager = $entityManager;
		$this->userPasswordHasher = $userPasswordHasher;
	}

	public function handle(User $user, FormInterface $form): void
	{
		/** @var string $plainPassword */
		$plainPassword = $form->get('password')->getData();

		$user->setPassword(
			$this->userPasswordHasher->hashPassword(
				$user,
				$plainPassword
			)
		);

		$this->entityManager->flush();
	}
}