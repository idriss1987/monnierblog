<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
       $admin = $this->createAdmin();

       $manager->persist($admin);

        $manager->flush();
    }

    private function createAdmin()
    {
        $admin = new User();

        $passwordHashed = $this->hasher->hashPassword($admin, "azerty1234A*");

        $admin->setLastName("Monnier");
        $admin->setFirstName("Pascal");
        $admin->setEmail("pascal-monnier@gmail.com");
        $admin->setPassword($passwordHashed);
        $admin->setRoles(array('ROLE_ADMIN', 'ROLE_USER'));
        $admin->setIsVerified(true);
        $admin->setVerifiedAt(new \DateTimeImmutable('now'));

        return $admin;
    }

}
