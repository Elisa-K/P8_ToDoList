<?php

namespace App\Handlers;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Service\Attribute\Required;

class HandlerManager implements HandlerInterface
{
	private FormInterface $form;

	private FormFactoryInterface $formFactory;

	#[Required]
	public function setFormFactoryInterface(FormFactoryInterface $formFactory): void
	{
		$this->formFactory = $formFactory;
	}

	public function handleForm(string $formType, object $data, Request $request): bool
	{
		if (null !== $this->formFactory) {
			$this->form = $this->formFactory->create($formType, $data)->handleRequest($request);

			if ($this->form->isSubmitted() && $this->form->isValid()) {
				return true;
			}
		}

		return false;
	}

	public function getForm(): FormInterface
	{
		return $this->form;
	}
}