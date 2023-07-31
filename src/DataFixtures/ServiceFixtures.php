<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class ServiceFixtures extends Fixture
{
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 3; $i++) {
            $service = new Service();
            $service->setName($faker->words(3, true))
            ->setDescription($faker->realText(550))
            ->setAmount(mt_rand(10, 300));

            $manager->persist($service);
        }

        $manager->flush();
    }
}
