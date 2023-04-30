<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Handlers\TaskHandlers\TaskAddHandler;
use Symfony\Component\HttpFoundation\Request;
use App\Handlers\TaskHandlers\TaskEditHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
        $form = $this->createForm(TaskType::class, $task)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($task);

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }
        return $this->render('task/create.html.twig', ['form' => $form]);

    }

    #[Route('/tasks/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function editAction(Task $task, Request $request, TaskEditHandler $handler): Response
    {
        $form = $this->createForm(TaskType::class, $task)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form,
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle', methods: ['GET'])]
    public function toggleTaskAction(Task $task, EntityManagerInterface $em): Response
    {
        $task->toggle(!$task->isDone());
        $em->flush();

        if ($task->isDone()) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        } else {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non faite.', $task->getTitle()));
        }


        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete', methods: ['DELETE'])]
    public function deleteTaskAction(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('TASK_DELETE', $task);
        // if ($this->isCsrfTokenValid('delete-' . $task->getId(), $request->get('_token'))) {

        try {
            $em->beginTransaction();
            $em->remove($task);
            $em->flush();
            $em->commit();

            $this->addFlash('success', 'La tâche a bien été supprimée.');
        } catch (\Exception $e) {
            $em->rollback();
            $this->addFlash('error', 'Une erreur s\'est produite, la tâche n\'a pu être supprimée !');
        }

        // }

        return $this->redirectToRoute('task_list');
    }

}