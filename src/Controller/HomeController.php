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
    
        $num = min(9, count($comments));
        $keys = array_rand($comments, $num);
    
        if (!is_array($keys)) {
            $keys = [$keys];
        }
    
        $selectedComments = [];
        foreach ($keys as $key) {
            $selectedComments[] = $comments[$key];
        }
    
        return $this->render('home/index.html.twig', [
            'users' => $users,
            'comments' => $selectedComments
        ]);
    }
}
