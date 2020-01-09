<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command;

use App\Security\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'app:user:create';

    /** @var UserManager */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        parent::__construct();
        $this->userManager = $userManager;
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The email')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password')
            ->addOption('role', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The roles')
            ->addOption('api-token', null, InputOption::VALUE_OPTIONAL, 'Api token');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getOption('password');
        $roles = $input->getOption('role');
        $apiToken = $input->getOption('api-token');

        if (empty($email)) {
            $email = $io->ask('Email? ');
        }

        if (empty($password)) {
            $password = $io->ask('Password? ');
        }

        $values = [
            'email' => $email,
            'password' => $password,
            'roles' => $roles,
        ];
        if (!empty($apiToken)) {
            $values['apiToken'] = $apiToken;
        }

        $user = $this->userManager->createUser($values);

        if (null === $user->getApiToken()) {
            $this->userManager->setApiToken($user);
        }

        $this->userManager->persistUser($user);

        $io->writeln('User created');
        $io->definitionList(
            ['id' => $user->getId()],
            ['email' => $user->getEmail()],
            ['roles' => implode(', ', $user->getRoles())],
            ['api token' => $user->getApiToken()],
        );

        return 0;
    }
}
