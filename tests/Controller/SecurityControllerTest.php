<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
	private ?KernelBrowser $client = null;

	public function setUp(): void
	{
		$this->client = static::createClient();
	}

	public function testLoginPage(): void
	{
		$this->client->request('GET', '/login');
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}

	public function testLoginSuccess(): void
	{
		$this->client->request('GET', '/login');
		$this->client->submitForm('Se connecter', [
			'_username' => 'user0',
			'_password' => 'password'
		]);

		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('a', 'Créer');
	}

	public function testLoginFail(): void
	{
		$this->client->request('GET', '/login');
		$this->client->submitForm('Se connecter', [
			'_username' => 'user0',
			'_password' => 'badPassword'
		]);
		$this->client->followRedirect();
		$this->assertSelectorExists('.alert-danger');
	}

	public function testLogout(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);

		$this->client->request('GET', '/logout');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
	}
}