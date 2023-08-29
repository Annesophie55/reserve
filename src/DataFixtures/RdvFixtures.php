<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Rdv;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class RdvFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Liste des créneaux horaires possibles
        $timeSlots = [];
        for ($hour = 9; $hour <= 20; $hour++) { // ajustez 21 pour votre heure de fin
        $timeSlots[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        $timeSlots[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':30';
        }


        for ($i = 0; $i < 20; $i++) {
            $rdv = new Rdv();

            // Choisissez un jour aléatoire
            $randomDay = $faker->dateTimeBetween('0 months', '+2 months');
            
            // Choisissez un créneau horaire aléatoire
            $randomTimeSlot = $timeSlots[array_rand($timeSlots)];

            // Créez un DateTimeImmutable basé sur le jour choisi et le créneau horaire
            $heureDebut = DateTimeImmutable::createFromFormat(
                'Y-m-d H:i',
                $randomDay->format('Y-m-d') . ' ' . $randomTimeSlot
            );

            $heureFin = $heureDebut->modify('+90 minutes');

            $user = $faker->numberBetween(18, 20);

            $details = $faker->text($maxNbChars = 200);

            $rdv->setCreatedAt(new DateTimeImmutable())
                ->setStatus($faker->boolean(true))
                ->setHeureDebut($heureDebut)
                ->setHeureFin($heureFin)

                ->setDetails($details);

            $manager->persist($rdv);
        }

        $manager->flush();
    }
}
