<?php

namespace App\Handlers\UserHandlers;

use App\Entity\User;
use App\Form\UserType;
use App\Handlers\HandlerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEditHandler
{
    private UserPasswordHasherInterface $userPasswordHasher;

    private EntityManagerInterface $entityManager;

    private HandlerManager $handlerManager;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    #[Required]
    public function setHandlerManager(HandlerManager $handlerManager): void
    {
        $this->handlerManager = $handlerManager;
    }


    public function handle(User $user, Request $request): bool
    {
        if ($this->handlerManager->handleForm(UserType::class, $user, $request)) {

            /** @var string $plainPassword */
            $plainPassword = $this->getForm()->get('password')->getData();

            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

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