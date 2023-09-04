<?php

namespace App\Controller;

use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils): Response
{
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    $response = $this->render('security/login.html.twig', [
        'last_username' => $lastUsername, 
        'error' => $error,
    ]);

    if ($this->getUser()) {  // Vérifiez si l'utilisateur est connecté
        $key = "your_secret_key_here";
        $payload = array(
            "iss" => "http://localhost:8080",
            "aud" => "http://localhost:8080",
            "iat" => time(),
            "nbf" => time(),
            "userId" => $this->getUser() // l'ID de l'utilisateur connecté
        );
        $jwt = JWT::encode($payload, $key, array('HS256'));
        
        // Créer un cookie et l'ajouter à la réponse
        $response->headers->setCookie(
            new Cookie('jwt', $jwt, (new \DateTime())->modify('+1 day'))
        );
    }

    return $response;
}

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
