<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Service;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{


#[Route('/user', name: 'app_user')]
public function index(Request $request, UserRepository $repo): Response
{
    $query = $request->query->get('query');
    if ($query) {
        $users = $repo->searchUsers($query);
        if (empty($users)) {
            $this->addFlash('warning', 'Aucun utilisateur correspondant à votre recherche.');
        }
    } else {
        $users = $repo->findAll();
    }

    return $this->render('user/index.html.twig', [
        'users' => $users,
    ]);
}

    #[Route('/user/select', name: 'app_user_select')]
    public function selectUser(Request $request, UserRepository $repo): Response
    {
            $users = $repo->findAll();
    
        return $this->render('components/_chatBubble.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route("/api/search/user/{query?}", name:"search_user")]
    public function searchUser(?string $query, UserRepository $repo): Response {
        if ($query) {
            $users = $repo->searchUsers($query);
        } else {
            $users = $repo->findAll();
        }
    
        $usersArray = [];
        foreach ($users as $user) {
            $usersArray[] = [
                'id' => $user->getId(),
                'name' => $user->getName(), // Ajustez en fonction de vos champs
                'email' => $user->getEmail() // Ajustez en fonction de vos champs
            ];
        }
        return new JsonResponse($usersArray);
    }
    

    

    #[Route('/user/{id}', name: 'app_user_show', methods:['GET','POST'])]
    public function show($id, UserRepository $userRepo, NoteRepository $noteRepository, NoteController $noteController, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepo->findOneBy(['id' => $id]);
        $notes = $noteRepository->findNotesByUserOrderedByDateDesc(['user' => $user]);
        
        // Appel de l'action 'new' de NoteController pour récupérer le formulaire
        $newNoteForm = $noteController->new($user->getId(), new Request(), $entityManager, $userRepo)->getContent();
    
        $notesData = [];
        foreach ($notes as $note) {
            $notesData[] = [
                'createdAt' => $note->getCreatedAt()->format('Y-m-d H:i:s'),
                'content' => $note->getContent(), 
            ];
        }
    
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'notesData' => $notesData,
            'newNoteForm' => $newNoteForm,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user');
    }
    
}
