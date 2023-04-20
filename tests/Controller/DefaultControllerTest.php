<?php

namespace App\Tests\Controller;


use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
	private ?KernelBrowser $client = null;

	public function setUp(): void
	{
		$this->client = static::createClient();
	}

	public function testHomepageLogged(): void
	{
		$userRepository = static::getContainer()->get(UserRepository::class);
		$testUser = $userRepository->findOneByEmail('user0@todo.com');
		$this->client->loginUser($testUser);
		$this->client->request('GET', '/');
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('a', 'Créer');
	}

	public function testHomepageNotLogged(): void
	{
		$this->client->request('GET', '/');
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
		$this->client->followRedirect();
		$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		$this->assertSelectorExists('label', 'Nom d\'utilisateur');
	}

}