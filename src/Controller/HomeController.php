<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(UserRepository $userRepository, CommentRepository $commentRepository): Response
    {
        $users = $userRepository->findAll();
        $comments = $commentRepository->findByTrue();
    
        $selectedComments = [];
    
        // Vérifie si le tableau de commentaires n'est pas vide.
        if (!empty($comments)) {
            $num = min(9, count($comments));
            $keys = array_rand($comments, $num);
    
            // Si il y a un seul commentaire, array_rand retourne la clé en tant qu'int.
            // Convertir la clé en tableau pour la compatibilité avec le code suivant.
            if (!is_array($keys)) {
                $keys = [$keys];
            }
    
            foreach ($keys as $key) {
                $selectedComments[] = $comments[$key];
            }
        }
        // Si le tableau de commentaires est vide, $selectedComments restera un tableau vide.
    
        return $this->render('home/index.html.twig', [
            'users' => $users,
            'comments' => $selectedComments
        ]);
    }
    
}
