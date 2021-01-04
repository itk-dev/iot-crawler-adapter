<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createUser(array $values = [])
    {
        $user = new User();

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($values as $name => $value) {
            if ('password' === $name) {
                $this->setPassword($user, $value);
            } else {
                $propertyAccessor->setValue($user, $name, $value);
            }
        }

        return $user;
    }

    /**
     * Create/update a user in the database.
     */
    public function persistUser(User $user, bool $fLush = true)
    {
        $this->entityManager->persist($user);
        if ($fLush) {
            $this->entityManager->flush();
        }
    }

    public function setPassword(User $user, string $password)
    {
        $this->validatePassword($password);

        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        return $user;
    }

    public function setApiToken(User $user, string $apiToken = null)
    {
        if (null === $apiToken) {
            $apiToken = sha1(random_bytes(32));
        }
        $this->validateApiToken($apiToken);

        $user->setApiToken($apiToken);

        return $user;
    }

    public function validatePassword($password)
    {
        if (empty($password) || !\is_string($password)) {
            throw new InvalidArgumentException('Invalid password');
        }
    }

    public function validateApiToken($apiToken)
    {
        if (empty($apiToken) || !\is_string($apiToken)) {
            throw new InvalidArgumentException('Invalid api token');
        }
    }
}
