<?php

namespace App\Test\Controller;

use App\Entity\Rdv;
use App\Repository\RdvRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RdvControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private RdvRepository $repository;
    private string $path = '/rdv/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Rdv::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Rdv index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'rdv[createdAt]' => 'Testing',
            'rdv[status]' => 'Testing',
            'rdv[dayHour]' => 'Testing',
            'rdv[duration]' => 'Testing',
        ]);

        self::assertResponseRedirects('/rdv/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Rdv();
        $fixture->setCreatedAt('My Title');
        $fixture->setStatus('My Title');
        $fixture->setDayHour('My Title');
        $fixture->setDuration('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Rdv');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Rdv();
        $fixture->setCreatedAt('My Title');
        $fixture->setStatus('My Title');
        $fixture->setDayHour('My Title');
        $fixture->setDuration('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'rdv[createdAt]' => 'Something New',
            'rdv[status]' => 'Something New',
            'rdv[dayHour]' => 'Something New',
            'rdv[duration]' => 'Something New',
        ]);

        self::assertResponseRedirects('/rdv/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getDayHour());
        self::assertSame('Something New', $fixture[0]->getDuration());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Rdv();
        $fixture->setCreatedAt('My Title');
        $fixture->setStatus('My Title');
        $fixture->setDayHour('My Title');
        $fixture->setDuration('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/rdv/');
    }
}
