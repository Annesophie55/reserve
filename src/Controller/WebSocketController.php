<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebSocketController extends AbstractController
{
    #[Route('/web/socket', name: 'app_web_socket')]
    public function index(): Response
    {
        return $this->render('web_socket/index.html.twig', [
            'controller_name' => 'WebSocketController',
        ]);
    }
}
