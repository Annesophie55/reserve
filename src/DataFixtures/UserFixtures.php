<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 15; $i++) {
            $user = new User();
            
            // Création d'un mot de passe avec Faker
            $plainPassword = $faker->password(8, 14);
            
            // Encodage du mot de passe
            $encodedPassword = $this->passwordEncoder->hashPassword($user, $plainPassword);

            
            $user->setName($faker->lastName(4, 12))
                ->setFirstName($faker->firstName(3, 15))
                ->setEmail($faker->unique()->safeEmail)
                ->setIsVerified($faker->boolean(true))
                ->setPhone($faker->phoneNumber())
                ->setPassword($encodedPassword)
                ->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
