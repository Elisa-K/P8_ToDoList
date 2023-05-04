<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TaskTest extends KernelTestCase
{

	private Task $task;

	private ContainerInterface $container;

	public function setUp(): void
	{
		self::bootKernel();
		$this->container = static::getContainer();

		$this->task = new Task();
		$this->task->setTitle('Titre d\'une tâche');
		$this->task->setContent('Contenu d\'une tâche');
		$this->task->setAuthor(new User());
	}

	public function testValidCreatedAt(): void
	{
		$this->assertInstanceOf(DateTimeImmutable::class, $this->task->getCreatedAt());
	}

	public function testDefaultIsDone(): void
	{
		$this->assertSame(false, $this->task->isDone());
	}

	public function testIsDoneFalse(): void
	{
		$this->task->toggle(false);
		$this->assertSame(false, $this->task->isDone());
	}

	public function testIsDoneTrue(): void
	{
		$this->task->toggle(true);
		$this->assertSame(true, $this->task->isDone());
	}

	public function testValidAuthor(): void
	{
		$this->assertInstanceOf(User::class, $this->task->getAuthor());
	}

	public function testValidTaskEntity(): void
	{
		$errors = $this->container->get('validator')->validate($this->task);
		$this->assertCount(0, $errors);
	}
	public function testEmptyTitle(): void
	{
		$this->task->setTitle('');

		$errors = $this->container->get('validator')->validate($this->task);
		$this->assertCount(2, $errors);
	}

	public function testTooShortTitle(): void
	{
		$this->task->setTitle('a');

		$errors = $this->container->get('validator')->validate($this->task);
		$this->assertCount(1, $errors);
	}

	public function testEmptyContent(): void
	{
		$this->task->setContent('');

		$errors = $this->container->get('validator')->validate($this->task);
		$this->assertCount(1, $errors);
	}

}