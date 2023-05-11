<?php

namespace App\Handlers;


use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class HandlerManager implements HandlerInterface
{
	private FormFactoryInterface $formFactory;
	private FormInterface $form;


	public function __construct(FormFactoryInterface $formFactory)
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