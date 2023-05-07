<?php

namespace App\Handlers;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface
{
    public function handleForm(string $formType, object $data, Request $request): bool;

    public function processAdd(object $data): void;

    public function processDelete(object $data): void;

    public function processUpdate(): void;

    public function getForm(): FormInterface;
}
