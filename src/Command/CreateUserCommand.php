<?php

namespace App\Command;

use App\Exception\UserExistException;
use App\Handler\UserHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    private UserHandler $userHandler;

    public function __construct(?string $name = null, UserHandler $userHandler)
    {
        parent::__construct($name);

        $this->userHandler = $userHandler;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The plain password of the user.')
            ->addArgument('role', InputArgument::OPTIONAL, 'The role of the user. (admin or user)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $roles = [];

        if ($input->getArgument('role') && $input->getArgument('role') === 'admin') {
            $roles = ['ROLE_ADMIN'];
        }

        try {
            $this->userHandler->createUser(
                $input->getArgument('email'),
                $input->getArgument('password'),
                $roles
            );

            $output->write('User as been created', true);

            return Command::SUCCESS;
        } catch (UserExistException $e) {
            $output->write('This email already used', true);

            return Command::SUCCESS;
        } catch (Throwable $th) {
            $output->write($th->getMessage());

            return Command::FAILURE;
        }
    }
}
