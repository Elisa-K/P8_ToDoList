<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
	private ?KernelBrowser $client = null;

	public function setUp(): void
	{
		$this->client = static::createClient();
	}

	public function testListUsersNotLogged(): void
	{
		$this->client->request('GET', '/users');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}

	public function testListUsersUserLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('GET', '/users');
		$this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
	}

	public function testListUsersAdminLogger(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testAdmin = $userRepository->findOneByEmail('admin@todo.com');
		$this->client->loginUser($testAdmin);

		$this->client->request('GET', '/users');
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
	}

	public function testCreateUserNotLogged(): void
	{
		$this->client->request('GET', '/users/create');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}

	public function testCreateUserUserLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('GET', '/users/create');
		$this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
	}

	public function testCreateUserAdminLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testAdmin = $userRepository->findOneByEmail('admin@todo.com');
		$this->client->loginUser($testAdmin);

		$this->client->request('GET', '/users/create');
		$this->client->submitForm('Ajouter', [
			'user[username]' => 'user6',
			'user[email]' => 'user6@todo.com',
			'user[password][first]' => 'password',
			'user[password][second]' => 'password',
			'user[roles]' => 'ROLE_USER'
		]);

		$this->assertResponseRedirects('/users', Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('.alert-success');
	}

	public function testCreateUserAdminLoggedError(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testAdmin = $userRepository->findOneByEmail('admin@todo.com');
		$this->client->loginUser($testAdmin);

		$this->client->request('GET', '/users/create');
		$this->client->submitForm('Ajouter', [
			'user[username]' => '',
			'user[email]' => 'user60@todo.com',
			'user[password][first]' => 'password',
			'user[password][second]' => 'password',
			'user[roles]' => 'ROLE_USER'
		]);

		$this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

	}

	public function testEditUserNotLogged(): void
	{
		$this->client->request('GET', '/users/1/edit');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}

	public function testEditUserUserLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('GET', '/users/1/edit');
		$this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
	}

	public function testEditUserAdminLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testAdmin = $userRepository->findOneByEmail('admin@todo.com');
		$this->client->loginUser($testAdmin);

		$testUser = $userRepository->findOneByEmail('user1@todo.com');

		$this->client->request('GET', '/users/' . $testUser->getId() . '/edit');
		$this->client->submitForm('Modifier', [
			'user[username]' => 'user1',
			'user[email]' => 'user1@todo.com',
			'user[password][first]' => 'password2',
			'user[password][second]' => 'password2',
			'user[roles]' => 'ROLE_USER'
		]);

		$this->assertResponseRedirects('/users', Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('.alert-success');
	}

	public function testEditUserAdminLoggedError(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testAdmin = $userRepository->findOneByEmail('admin@todo.com');
		$this->client->loginUser($testAdmin);

		$testUser = $userRepository->findOneByEmail('user1@todo.com');

		$this->client->request('GET', '/users/' . $testUser->getId() . '/edit');
		$this->client->submitForm('Modifier', [
			'user[username]' => 'u',
			'user[email]' => 'user1@todo.com',
			'user[password][first]' => 'password2',
			'user[password][second]' => 'password2',
			'user[roles]' => 'ROLE_USER'
		]);

		$this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

	}

}