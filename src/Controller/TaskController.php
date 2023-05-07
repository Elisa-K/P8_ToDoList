<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Handlers\TaskHandlers\TaskAddHandler;
use Symfony\Component\HttpFoundation\Request;
use App\Handlers\TaskHandlers\TaskEditHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Handlers\TaskHandlers\TaskDeleteHandler;
use App\Handlers\TaskHandlers\TaskToggleHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'task_list', methods: ['GET'])]
    public function listAction(TaskRepository $taskRepository): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskRepository->findBy(['isDone' => false]),
        ]);
    }

    #[Route('/tasks/done', name: 'task_list_done', methods: ['GET'])]
    public function listActionDone(TaskRepository $taskRepository): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskRepository->findBy(['isDone' => true]),
        ]);
    }

    #[Route('/tasks/create', name: 'task_create', methods: ['GET', 'POST'])]
    public function createAction(Request $request, TaskAddHandler $handler): Response
    {
        $task = new Task();

        if ($handler->handle($task, $request)) {
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $handler->getForm()]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function editAction(Task $task, Request $request, TaskEditHandler $handler): Response
    {
        if ($handler->handle($task, $request)) {
            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $handler->getForm(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle', methods: ['POST'])]
    public function toggleTaskAction(Task $task, TaskToggleHandler $handler): Response
    {
        if ($handler->handle($task)) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        } else {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non faite.', $task->getTitle()));
        }

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete', methods: ['DELETE'])]
    public function deleteTaskAction(Task $task, Request $request, TaskDeleteHandler $handler): Response
    {
        $this->denyAccessUnlessGranted('TASK_DELETE', $task);

        /** @var string $token */
        $token = $request->get('_token');

        if ($this->isCsrfTokenValid('delete-' . $task->getId(), $token)) {

            $handler->handle($task);

            $this->addFlash('success', 'La tâche a bien été supprimée.');
        }

        return $this->redirectToRoute('task_list');
    }
}