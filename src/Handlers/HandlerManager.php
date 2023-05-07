<?php

namespace App\Handlers;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class HandlerManager implements HandlerInterface
{
    private ?FormFactoryInterface $formFactory = null;
    private FormInterface $form;
    private EntityManagerInterface $entityManager;

    public function __construct(?FormFactoryInterface $formFactory, EntityManagerInterface $entityManager)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
    }

    public function handleForm(string $formType, object $data, Request $request): bool
    {
        if (null != $this->formFactory) {
            $this->form = $this->formFactory->create($formType, $data)->handleRequest($request);

            if ($this->form->isSubmitted() && $this->form->isValid()) {
                return true;
            }
        }

        return false;
    }

    public function processAdd(object $data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function processDelete(object $data): void
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }

    public function processUpdate(): void
    {
        $this->entityManager->flush();
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }
}
