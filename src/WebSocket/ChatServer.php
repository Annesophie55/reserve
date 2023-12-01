<?php

namespace App\WebSocket;

// require '../vendor/autoload.php';

use App\Entity\User;
use Firebase\JWT\JWT;
use App\Entity\Message;
use App\Entity\Conversation;
use Ratchet\ConnectionInterface;
use App\Repository\UserRepository;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;
use Ratchet\MessageComponentInterface;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\SignatureInvalidException;

class ChatServer implements MessageComponentInterface {
    protected $clients;
    protected $entityManager;
    protected $userRepository;

    // Table d'association pour lier les objets ConnectionInterface aux utilisateurs
    protected $userConnections;
    
    // Définir la clé comme propriété privée (vous pouvez également la définir comme constante)
    private $key = "your_secret_key_here";

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository) {
        $this->clients = new \SplObjectStorage;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function onOpen(ConnectionInterface $conn) {
        $cookies = $conn->WebSocket->request->getCookies();
        $jwt = $cookies['jwt'] ?? null;

        if (!$jwt) {
            $conn->close();
            return;
        }

        try {
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            $userId = $decoded->userId;
        } catch (ExpiredException $e) {
            $conn->close();
            return;
        } catch (SignatureInvalidException $e) {
            $conn->close();
            return;
        } catch (BeforeValidException $e) {
            $conn->close();
            return;
        } catch (\UnexpectedValueException $e) {
            $conn->close();
            return;
        } catch (\Exception $e) {
            $conn->close();
            return;
        }

        $this->userConnections[$userId] = $conn;
        $this->clients->attach($conn);
    }
    

    public function onMessage(ConnectionInterface $from, $receivedMessage) {
        var_dump("Message reçu: $receivedMessage"); 
        $data = json_decode($receivedMessage);
        var_dump($data);

        // Création du nouvel objet Message
        $message = new Message();
        $message->setContent($data->messageContent);
        $message->setSender($data->senderId);
        $message->setRecipient($data->recipientId);
        $message->setCreatedAt(new \DateTimeImmutable());
    
        // Enregistrement en BDD
        try {
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            error_log("Could not save message: " . $e->getMessage());
        }
        

        // Trouvez les connexions pour l'expéditeur et le destinataire
        $senderConnection = $this->userConnections[$data->senderId] ?? null;
        $recipientConnection = $this->userConnections[$data->recipientId] ?? null;

        // Envoyez le message uniquement à l'expéditeur et au destinataire
        if ($senderConnection) {
            $senderConnection->send($receivedMessage);
        }
        if ($recipientConnection) {
            $recipientConnection->send($receivedMessage);
        }
    }
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}