<?php

namespace App\Tests\Controller;

use App\Entity\Rdv;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

class RdvControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private string $path = '/rdv/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Rdv index', $crawler->filter('h1')->text());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->entityManager->getRepository(Rdv::class)->findAll());

        $crawler = $this->client->request('GET', $this->path . 'new');

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form([
            'rdv[createdAt]' => '2023-09-19',
            'rdv[status]' => 'My Status',
            'rdv[heure_debut]' => '2023-09-19 10:30:00',
            'rdv[heure_fin]' => '2023-09-19 12:00:00',
        ]);

        $this->client->submit($form);

        self::assertTrue($this->client->getResponse()->isRedirect('/rdv/'));

        $newNumObjectsInRepository = count($this->entityManager->getRepository(Rdv::class)->findAll());

        self::assertSame($originalNumObjectsInRepository + 1, $newNumObjectsInRepository);
    }

    public function testShow(): void
    {
        $fixture = new Rdv();
        $fixture->setCreatedAt(new DateTimeImmutable());
        $fixture->setStatus('My Status');
        $fixture->setHeureDebut(new DateTimeImmutable('2023-09-19 10:00:00'));
        $fixture->setHeureFin(new DateTimeImmutable('2023-09-19 11:30:00'));

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        $this->client->request('GET', $this->path . $fixture->getId());

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Rdv', $this->client->getResponse()->getContent());

        // You can add assertions to check that the properties of the Rdv entity are displayed correctly.
    }

    public function testEdit(): void
    {
        $fixture = new Rdv();
        $fixture->setCreatedAt(new DateTimeImmutable());
        $fixture->setStatus('My Status');
        $fixture->setHeureDebut(new DateTimeImmutable('2023-09-19 10:00:00'));
        $fixture->setHeureFin(new DateTimeImmutable('2023-09-19 11:30:00'));

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->path . $fixture->getId() . '/edit');

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Update')->form([
            'rdv[createdAt]' => '2023-09-20',
            'rdv[status]' => 'Updated Status',
            'rdv[heure_debut]' => '2023-09-20 11:00:00',
            'rdv[heure_fin]' => '2023-09-20 12:30:00',
        ]);

        $this->client->submit($form);

        self::assertTrue($this->client->getResponse()->isRedirect('/rdv/'));

        // Fetch the updated entity from the database
        $updatedFixture = $this->entityManager->getRepository(Rdv::class)->find($fixture->getId());

        self::assertInstanceOf(DateTimeImmutable::class, $updatedFixture->getCreatedAt());
        self::assertSame(1, $updatedFixture->getStatus());
        self::assertInstanceOf(DateTimeImmutable::class, $updatedFixture->getHeureDebut());
        self::assertInstanceOf(DateTimeImmutable::class, $updatedFixture->getHeureFin());
    }

    public function testRemove(): void
    {
        $fixture = new Rdv();
        $fixture->setCreatedAt(new DateTimeImmutable());
        $fixture->setStatus(1);
        $fixture->setHeureDebut(new DateTimeImmutable('2023-09-19 10:00:00'));
        $fixture->setHeureFin(new DateTimeImmutable('2023-09-19 11:00:00'));
    }
}    
