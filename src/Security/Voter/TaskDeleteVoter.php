<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


class TaskDeleteVoter extends Voter
{

	public const DELETE = 'TASK_DELETE';

	private $security;

	public function __construct(Security $security)
	{
		$this->security = $security;
	}

	protected function supports(string $attribute, mixed $subject): bool
	{
		return in_array($attribute, [self::DELETE]) && $subject instanceof Task;
	}

	protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
	{
		$user = $token->getUser();
		if (!$user instanceof UserInterface) {
			return false;
		}

		switch ($attribute) {
			case self::DELETE:
				return $this->canDelete($subject, $user);
				break;
		}
		return false;
	}

	private function canDelete(Task $task, User $user): bool
	{
		if ($user === $task->getAuthor()) {
			return true;
		}

		if ($task->getAuthor() === null && $this->security->isGranted('ROLE_ADMIN')) {
			return true;
		}

		return false;
	}

}