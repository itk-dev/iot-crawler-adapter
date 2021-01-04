<?php

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataFixtures;

use App\Entity\User;
use App\Security\UserManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    /** @var UserManager */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function load(ObjectManager $manager)
    {
        $user = (new User())
            ->setEmail('loriot@example.com')
            ->setApiToken('api-test-loriot')
            ->setRoles(['ROLE_LORIOT']);
        $this->userManager->setPassword($user, 'loriot');

        $manager->persist($user);
        $this->setReference('user:loriot', $user);

        $user = (new User())
            ->setEmail('montem@example.com')
            ->setApiToken('api-test-montem')
            ->setRoles(['ROLE_MONTEM']);
        $this->userManager->setPassword($user, 'montem');

        $manager->persist($user);
        $this->setReference('user:smartcitizen', $user);

        $user = (new User())
            ->setEmail('smartcitizen@example.com')
            ->setApiToken('api-test-smartcitizen')
            ->setRoles(['ROLE_SMARTCITIZEN']);
        $this->userManager->setPassword($user, 'smartcitizen');

        $manager->persist($user);
        $this->setReference('user:smartcitizen', $user);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test', 'user'];
    }
}
