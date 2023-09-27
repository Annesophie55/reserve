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
            // Récupérez l'utilisateur actuellement connecté.
            $user = $this->getUser();
            $content = $request->request->get('content');
        
            if (!$user) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un commentaire.');
            }
    
            // Liez le commentaire à l'utilisateur et définissez les autres champs.
            $comment->setUser($user)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setContent($content);
            
            $entityManager->persist($comment);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le commentaire a bien était ajouté!');
            return $this->redirectToRoute('app_home');
        }
    
        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
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

    #[Route('/{id}/toggle', name: 'toggle_comment_valid', methods: ['POST'])]
    public function toggleCommentValid(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $isValid = $request->request->get('commentSlider');
    
        // Mettez à jour l'état is_valid en fonction de la valeur du curseur
        $comment->setIsValid((bool) $isValid);
    
        // Enregistrez les modifications en base de données
        $entityManager->flush();
    
        // Redirigez l'utilisateur vers la page d'origine ou une autre page
        return $this->redirectToRoute('app_comment_index');
    }
    
}
