<?php

namespace App\Handlers;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface
{
    public function handleForm(string $formType, object $data, Request $request): bool;

    public function getForm(): FormInterface;
}