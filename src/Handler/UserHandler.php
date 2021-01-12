<?php

namespace App\Handler;

use App\Entity\User;
use App\Exception\UserExistException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserHandler
{
    private UserPasswordEncoderInterface $encoder;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * For create new user
     *
     * @param string     $email
     * @param string     $password
     * @param array|null $roles
     *
     * @throws UserExistException
     *
     * @return void
     */
    public function createUser(string $email, string $password, ?array $roles = [])
    {
        if ($this->userRepository->findOneBy(['email' => $email])) {
            throw new UserExistException();
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $user->setRoles($roles);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
