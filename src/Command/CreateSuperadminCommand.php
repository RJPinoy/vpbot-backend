<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\User;

#[AsCommand(
    name: 'app:create-superadmin',
    description: 'Creates a superadmin user if it does not exist',
)]
class CreateSuperadminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Superadmin email')
            ->addArgument('password', InputArgument::REQUIRED, 'Superadmin password');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email  = $input->getArgument('email');
        $pass   = $input->getArgument('password');

        $existing = $this->userRepository->findOneBy(['email' => $email]);

        if ($existing) {
            $io->warning("User with email $email already exists.");
            return self::SUCCESS;
        }

        $user = new User();
        $user->setFirstName('Super');
        $user->setLastName('Admin');
        $user->setEmail($email);
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setUsername('superadmin');
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setLastConnected(null);
        $user->setPicture('/assets/images/avatar/avatar.jpg');
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $pass)
        );

        $this->em->persist($user);
        $this->em->flush();

        $io->success("Superadmin $email created.");

        return self::SUCCESS;
    }
}
