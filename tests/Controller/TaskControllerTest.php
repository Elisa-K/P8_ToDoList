<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
	private ?KernelBrowser $client = null;

	public function setUp(): void
	{
		$this->client = static::createClient();
	}

	public function testListTasksNotLogged(): void
	{
		$this->client->request('GET', '/tasks');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}

	public function testListTasksLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('GET', '/tasks');
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
	}

	public function testListTasksDoneNotLogged(): void
	{
		$this->client->request('GET', '/tasks/done');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}

	public function testListTasksDoneLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('GET', '/tasks/done');
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
	}

	public function testCreateTaskNotLogged(): void
	{
		$this->client->request('GET', '/tasks/create');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}

	public function testCreateTaskLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('GET', '/tasks/create');
		$this->client->submitForm('Ajouter', [
			'task[title]' => 'test',
			'task[content]' => 'test content'
		]);

		$this->assertResponseRedirects('/tasks', Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('.alert-success');
	}

	public function testCreateTaskLoggedError(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('GET', '/tasks/create');
		$this->client->submitForm('Ajouter', [
			'task[title]' => '',
			'task[content]' => 'test content'
		]);

		$this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	public function testEditTaskNotLogged(): void
	{
		$this->client->request('GET', '/tasks/2/edit');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}

	public function testEditTaskLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('GET', '/tasks/2/edit');
		$this->client->submitForm('Modifier', [
			'task[title]' => 'test edit',
			'task[content]' => 'test content edit'
		]);

		$this->assertResponseRedirects('/tasks', Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('.alert-success');
	}

	public function testEditTaskLoggedError(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('GET', '/tasks/2/edit');
		$this->client->submitForm('Modifier', [
			'task[title]' => 't',
			'task[content]' => 'test content edit'
		]);

		$this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	public function testToggleTaskNotLogged(): void
	{
		$this->client->request('POST', '/tasks/2/toggle');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}
	public function testToggleTaskLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('POST', '/tasks/2/toggle');
		$this->assertResponseRedirects('/tasks', Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('.alert-success');
	}

	public function testDeleteTaskNotLogged(): void
	{
		$this->client->request('DELETE', '/tasks/2/delete');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}

	public function testDeleteTaskByAuthorLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$taskRepository = static::getContainer()->get(TaskRepository::class);
		$testTask = $taskRepository->findOneBy(['author' => $testUser]);

		$this->client->request('DELETE', '/tasks/' . $testTask->getId() . '/delete');
		$this->assertResponseRedirects('/tasks', Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('.alert-success');

	}

	public function testDeleteTaskNotByAuthorLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$testAuthor = $userRepository->findOneByEmail('user1@todo.com');

		$taskRepository = static::getContainer()->get(TaskRepository::class);
		$testTask = $taskRepository->findOneBy(['author' => $testAuthor]);

		$this->client->request('DELETE', '/tasks/' . $testTask->getId() . '/delete');
		$this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
	}

	public function testDeleteAnonymeTaskByAdmin(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testAdmin = $userRepository->findOneByEmail('admin@todo.com');
		$this->client->loginUser($testAdmin);

		$taskRepository = static::getContainer()->get(TaskRepository::class);
		$testTask = $taskRepository->findOneBy(['author' => null]);

		$this->client->request('DELETE', '/tasks/' . $testTask->getId() . '/delete');
		$this->assertResponseRedirects('/tasks', Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('.alert-success');

	}

	public function testDeleteAnonymeTaskByUser(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$taskRepository = static::getContainer()->get(TaskRepository::class);
		$testTask = $taskRepository->findOneBy(['author' => null]);

		$this->client->request('DELETE', '/tasks/' . $testTask->getId() . '/delete');

		$this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
	}
}