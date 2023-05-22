<?php

namespace App\Controller;

use App\Entity\User;
use App\Handlers\UserHandlers\UserAddHandler;
use App\Handlers\UserHandlers\UserEditHandler;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'user_list', methods: ['GET'])]
    public function listAction(UserRepository $userRepository): Response
    {
        return $this->render('user/list.html.twig', ['users' => $userRepository->findAll()]);
    }

    #[Route('/users/create', name: 'user_create', methods: ['GET', 'POST'])]
    public function register(Request $request, UserAddHandler $handler): Response
    {
        $user = new User();

        if ($handler->handle($user, $request)) {
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $handler->getForm(),
        ]);
    }

    #[Route('/users/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function editAction(User $user, Request $request, UserEditHandler $handler): Response
    {
        if ($handler->handle($user, $request)) {
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $handler->getForm(), 'user' => $user]);
    }
}