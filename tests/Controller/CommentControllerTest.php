<?php

namespace App\Test\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use DateTimeImmutable;
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

    // public function testIndex(): void
    // {
    //     $crawler = $this->client->request('GET', $this->path);

    //     self::assertResponseStatusCodeSame(200);
    //     self::assertSelectorTextContains('h1', 'Liste des commentaires');

      
    // }


    public function testNew(): void
    {
        // 1. Se connecter en tant qu'utilisateur
        $userRepository = $this->manager->getRepository(User::class);
        $user = $userRepository->findOneByEmail('manilouve@gmail.fr');
        $this->client->loginUser($user);
    
        // 2. Accéder à la page de création de commentaire
        $crawler = $this->client->request('GET', sprintf('%snew', $this->path));
        self::assertResponseStatusCodeSame(200);
    
        // 3. Soumettre le formulaire
        $form = $crawler->selectButton('save')->form([
            'comment[title]' => 'Testing',
            'comment[content]' => 'Testing content',
        ]);
        $this->client->submit($form);
    
        // 4. Vérifier si le commentaire a été créé
        $newNumObjectsInRepository = count($this->repository->findAll());
        $this->assertSame(1, $newNumObjectsInRepository);
    
        $lastComment = $this->repository->findOneBy([], ['createdAt' => 'DESC']);
        self::assertSame('Testing content', $lastComment->getContent());
    
        // Vérifiez que la redirection est correcte
        self::assertResponseRedirects('/');
    }
    
    // public function testShow(): void
    // {
    //     $this->markTestIncomplete();
    //     $fixture = new Comment();
    //     $fixture->setCreatedAt(new DateTimeImmutable());
    //     $fixture->setTitle('My Title');
    //     $fixture->setContent('My Title');
    //     $fixture->setUser('20');

    //     $this->manager->persist($fixture);
    //     $this->manager->flush();

    //     $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

    //     self::assertResponseStatusCodeSame(200);
    //     self::assertPageTitleContains('Comment');

    //     // Use assertions to check that the properties are properly displayed.
    // }

    // public function testEdit(): void
    // {
    //     $this->markTestIncomplete();
    //     $fixture = new Comment();
    //     $fixture->setCreatedAt(new DateTimeImmutable);
    //     $fixture->setTitle('My Title');
    //     $fixture->setContent('My Title');
    //     $fixture->setUser(new User);

    //     $this->manager->persist($fixture);
    //     $this->manager->flush();

    //     $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

    //     $this->client->submitForm('Update', [
    //         'comment[createdAt]' => new DateTimeImmutable,
    //         'comment[title]' => 'Something New',
    //         'comment[content]' => 'Something New',
    //         'comment[user]' => new User,
    //     ]);

    //     self::assertResponseRedirects('/comment/');

    //     $fixture = $this->repository->findAll();

    //     self::assertSame(new DateTimeImmutable, $fixture[0]->getCreatedAt());
    //     self::assertSame('Something New', $fixture[0]->getTitle());
    //     self::assertSame('Something New', $fixture[0]->getContent());
    //     self::assertSame('Something New', $fixture[0]->getUser());
    // }

    // public function testRemove(): void
    // {
    //     $this->markTestIncomplete();

    //     $originalNumObjectsInRepository = count($this->repository->findAll());

    //     $fixture = new Comment();
    //     $fixture->setCreatedAt(new DateTimeImmutable);
    //     $fixture->setTitle('My Title');
    //     $fixture->setContent('My Title');
    //     $fixture->setUser(new User);

    //     $this->manager->persist($fixture);
    //     $this->manager->flush();

    //     self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

    //     $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
    //     $this->client->submitForm('Delete');

    //     self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
    //     self::assertResponseRedirects('/comment/');
    // }
}
