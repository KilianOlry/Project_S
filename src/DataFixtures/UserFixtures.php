<?php

namespace App\DataFixtures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setPassword($this->hasher->hashPassword($admin, 'Admin45140$$$'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPrenom('Admin');
        $admin->setNom('NomAdmin');
        $admin->setTelephone("0238795852");
        $admin->setMetier('Administrateur');

        $manager->persist($admin);
        $manager->flush();
    }
}
