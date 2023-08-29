<?php

namespace App\Controller;

use DateTime;
use App\Entity\Rdv;
use App\Entity\Note;
use App\Entity\User;
use App\Form\NoteType;
use DateTimeImmutable;
use App\Repository\RdvRepository;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/note')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_index', methods: ['GET'])]
    public function index(NoteRepository $noteRepository): Response
    {
        return $this->render('note/index.html.twig', [
            'notes' => $noteRepository->findAll(),
        ]);
    }

    #[Route('/new/{user}', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new($user, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepo): Response
{
    $user = $userRepo->find($user);

    if (!$user) {
        return $this->redirectToRoute('app_home');
    }

    $note = new Note();
    $form = $this->createForm(NoteType::class, $note, [
        'user' => $user,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        $formData = $request->request->all()['note']; // Notez le ['note'], qui est le nom du formulaire
        $user = $entityManager->getRepository(User::class)->find($formData['userId']);

        
        if ($user) {
            $note->setUser($user);

        }
    
        if ($form->isValid()) {
            $note->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($note);
            $entityManager->flush();
            return $this->redirectToRoute('app_user');
        }
    }
    
    return $this->render('note/new.html.twig', [
        'note' => $note,
        'form' => $form,
    ]);
}


    #[Route('/{id}/edit', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/index.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $entityManager->remove($note);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
    }



}




