<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\PrivateChatbot;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // or 'en_US' for English

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail())
                ->setUsername($faker->userName())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setRoles(['ROLE_USER'])
                ->setCreatedAt(new \DateTimeImmutable())
                ->setLastConnected($faker->dateTimeBetween('-1 year', 'now'))
                ->setPicture('/assets/images/avatar/avatar.jpg');

            // Hash the password 'test1234'
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'test1234');
            $user->setPassword($hashedPassword);

            $manager->persist($user);

            $private_chatbot = new PrivateChatbot();
            $private_chatbot->setApiKey('sk-...')
                ->setModel('gpt-4o-mini')
                ->setUserChatbot($user);

            $manager->persist($private_chatbot);
        }

        $manager->flush();
    }
}
