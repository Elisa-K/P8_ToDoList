<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $users = [];

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@todo.com');
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for ($i = 0; $i < 4; ++$i) {
            $user = new User();
            $user->setUsername("user$i");
            $user->setEmail("user$i@todo.com");
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
            $users[] = $user;
            $manager->persist($user);
        }
        // Tâche anonyme
        $users[] = null;

        for ($j = 0; $j < 30; ++$j) {
            $task = new Task();
            $task->setTitle($faker->sentence());
            $task->setContent($faker->paragraph());
            /** @var User|null $author */
            $author = $faker->randomElement($users);
            $task->setAuthor($author);
            $author === null ? $task->toggle(false) : $task->toggle($faker->boolean());
            $manager->persist($task);
        }

        $manager->flush();
    }
}