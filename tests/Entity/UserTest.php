<?php

namespace App\Tests\Entity;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserTest extends KernelTestCase
{

	private User $user;
	private ContainerInterface $container;

	public function setUp(): void
	{
		self::bootKernel();
		$this->container = static::getContainer();

		$this->user = new User();
		$this->user->setUsername('pseudo');
		$this->user->setEmail('pseudo@email.com');
		$this->user->setPassword('1234');
	}

	public function testDefaultRoleUser(): void
	{
		$this->assertSame(['ROLE_USER'], $this->user->getRoles());
	}

	public function testUserHasRoleAdmin(): void
	{
		$this->user->setRoles(['ROLE_ADMIN']);
		$this->assertContains('ROLE_ADMIN', $this->user->getRoles());
	}

	public function testValidUserEntity(): void
	{
		$errors = $this->container->get('validator')->validate($this->user);
		$this->assertCount(0, $errors);
	}

	public function testEmptyUsername(): void
	{
		$this->user->setUsername('');

		$errors = $this->container->get('validator')->validate($this->user);
		$this->assertCount(2, $errors);
	}

	public function testTooShortUsername(): void
	{
		$this->user->setUsername('a');

		$errors = $this->container->get('validator')->validate($this->user);
		$this->assertCount(1, $errors);
	}

	public function testTooLongUsername(): void
	{
		$this->user->setUsername(str_repeat('a', 26));
		$errors = $this->container->get('validator')->validate($this->user);
		$this->assertCount(1, $errors);
	}

	public function testUniqueUsername(): void
	{
		$this->user->setUsername('user0');
		$errors = $this->container->get('validator')->validate($this->user);
		$this->assertCount(1, $errors);
	}

	public function testEmptyEmail(): void
	{
		$this->user->setEmail('');

		$errors = $this->container->get('validator')->validate($this->user);
		$this->assertCount(1, $errors);
	}

	public function testInvalidFormatEmail(): void
	{
		$this->user->setEmail('a@mail');

		$errors = $this->container->get('validator')->validate($this->user);
		$this->assertCount(1, $errors);
	}

	public function testUniqueEmail(): void
	{
		$this->user->setEmail('user0@todo.com');
		$errors = $this->container->get('validator')->validate($this->user);
		$this->assertCount(1, $errors);
	}

	public function testEmptyPassword(): void
	{
		$this->user->setPassword('');

		$errors = $this->container->get('validator')->validate($this->user);
		$this->assertCount(1, $errors);
	}


}