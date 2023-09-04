<?php 
// bin/chat-server.php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use App\WebSocket\ChatServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

require dirname(__DIR__) . '/vendor/autoload.php';

// Active le mode de debug si nécessaire
Debug::enable();

// Charge les variables d'environnement si vous utilisez .env
(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

// Initialise le kernel
$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

// Obtenez le conteneur
$kernel->boot();
$container = $kernel->getContainer();

// Maintenant, nous pouvons obtenir EntityManager et UserRepository
$entityManager = $container->get('doctrine')->getManager();
$userRepository = $entityManager->getRepository(\App\Entity\User::class);


// Votre logique pour démarrer le serveur WebSocket reste la même


// Créez le serveur de chat avec les dépendances.
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer($entityManager, $userRepository)
        )
    ),
    8080
);

$server->run();
