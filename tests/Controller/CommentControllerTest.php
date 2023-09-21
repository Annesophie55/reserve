<?php

namespace App\Test\Controller;

use App\Entity\Comment;
use App\Entity\User;
namespace App\Test\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CommentRepository $repository;
    private string $path = '/comment/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get(CommentRepository::class);
        $this->manager = static::getContainer()->get(EntityManagerInterface::class);

      
   
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('h1', 'Liste des commentaires');

      
    }


    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'comment[createdAt]' => '2023-09-19 10:00:00',
            'comment[title]' => 'Testing',
            'comment[content]' => 'Testing',
            'comment[user]' => 20,
        ]);

        self::assertResponseRedirects('/comment/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Comment();
        $fixture->setCreatedAt('2023-09-19 10:00:00');
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setUser('20');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Comment');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Comment();
        $fixture->setCreatedAt(new DateTimeImmutable);
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setUser(new User);

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'comment[createdAt]' => new DateTimeImmutable,
            'comment[title]' => 'Something New',
            'comment[content]' => 'Something New',
            'comment[user]' => new User,
        ]);

        self::assertResponseRedirects('/comment/');

        $fixture = $this->repository->findAll();

        self::assertSame(new DateTimeImmutable, $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Comment();
        $fixture->setCreatedAt(new DateTimeImmutable);
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setUser(new User);

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/comment/');
    }
}
