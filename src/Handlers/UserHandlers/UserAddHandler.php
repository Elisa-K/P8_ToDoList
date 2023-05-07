<?php

namespace App\Handlers\UserHandlers;

use App\Entity\User;
use App\Form\UserType;
use App\Handlers\HandlerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAddHandler extends HandlerManager
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct($formFactory, $entityManager);
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function handle(User $user, Request $request): bool
    {
        if ($this->handleForm(UserType::class, $user, $request)) {
            /** @var string $plainPassword */
            $plainPassword = $this->getForm()->get('password')->getData();

            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            $this->processAdd($user);

            return true;
        }

        return false;
    }
}
