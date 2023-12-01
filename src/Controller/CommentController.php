<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un commentaire.');
            }

            $comment->setUser($user)
                    ->setCreatedAt(new \DateTimeImmutable());
            
            $entityManager->persist($comment);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le commentaire a bien était ajouté!');
            return $this->redirectToRoute('app_home');
        }
    
        return $this->render('comment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    


    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{commentId}/toggle', name: 'toggle_comment_valid', methods: ['GET'])]
    public function toggleValid(Request $request, int $commentId, EntityManagerInterface $entityManager): Response
    {
        // Récupérez le commentaire existant depuis la base de données en fonction de l'ID
        $comment = $entityManager->getRepository(Comment::class)->find($commentId);
        
        if (!$comment) {
            throw $this->createNotFoundException('Commentaire introuvable');
        }
    
        $isValid = $request->query->get('isValid');
        
        $comment->setIsValid((bool) $isValid);
    
    
        $entityManager->flush();
    
        return $this->redirectToRoute('app_comment_index');
    }
    
}
