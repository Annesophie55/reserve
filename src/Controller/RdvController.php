<?php

namespace App\Controller;


use DateTime;
use Exception;
use App\Entity\Rdv;
use App\Entity\User;
use App\Form\RdvType;
use DateTimeImmutable;
use DateTimeInterface;
use App\Repository\RdvRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// #[Route('/rdv')]
class RdvController extends AbstractController
{

    #[Route('/appointements', name: 'app_rdv_appointements', methods: ['GET'])]
    public function appointements(Request $request)
{
    return $this->render('rdv/_form.html.twig');
}

#[Route('/appointements', name: 'app_rdv_events', methods: ['GET'])]
public function getEvents(RdvRepository $repo)
{
    

    $events = $repo->findAll();
    

    $formattedEvents = array_map(function($event) {
        return [
            'title' => 'Réservation', // vous pouvez personnaliser ceci
            'start' => $event->getHeureDebut()->format('Y-m-d H:i:s'),
            'end' => $event->getHeureFin()->format('Y-m-d H:i:s'),
        ];
    }, $events);

    return $this->json($formattedEvents);
}

#[Route("/available-slots", name:"available_slots", methods:['GET'])
]
public function availableSlots(Request $request, EntityManagerInterface $em)
{

$dateString = $request->query->get('date');
$date = new \DateTimeImmutable($dateString);


    $dayOfWeek = $date->format('N'); // 1 = Monday, 2 = Tuesday, ..., 7 = Sunday




    $availableSlots = [];

    if (in_array($dayOfWeek, [1, 2, 4, 5])) {  
        $startHour = 9;  // 9am
        $endHour = 20.50; // 8:30pm
        

        for ($hour = $startHour; $hour <= $endHour; $hour += 0.50) {
            $slotStart = clone $date;
            $slotStart = $slotStart->setTime(floor($hour), ($hour * 60) % 60); 

        $slotEnd = clone $slotStart;
$slotEnd = $slotEnd->modify('+1 hour 30 minutes');   
$endTime = clone $slotStart;
$endTime = $endTime->modify('+1 hour 30 minutes');




$existingReservations = $em->getRepository(Rdv::class)
    ->findOverlappingReservations($slotStart, $endTime);

if (count($existingReservations) === 0) {
    $availableSlots[] = [
        'start' => $slotStart->format('Y-m-d H:i:s'),
        'end' => $endTime->format('Y-m-d H:i:s'),
        'rendering' => 'background',
    ];
}

        }
    }

    return $this->json($availableSlots);
}


#[Route("/reserve-slot", name:"reserve_slot", methods:["POST"])]
public function reserveSlot(Request $request, EntityManagerInterface $em,UserRepository $userRepository)
{
    $pattern = '/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})-(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})$/';
    $selectedSlotsStr = $request->request->get('selectedSlot');
    
    if (preg_match($pattern, $selectedSlotsStr, $matches)) {
        $startTimeStr = $matches[1];
        $endTimeStr = $matches[2];
        
        $startTime = new \DateTimeImmutable($startTimeStr);
        $endTime = new \DateTimeImmutable($endTimeStr);

        $currentUser = $this->getUser();
        $currentRole = $currentUser->getRoles();

        $reservation = new Rdv();

        if (in_array("ROLE_ADMIN", $currentRole)) {
            $user_email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $user_email]);
            $reservation->setUser($user);
        } else {
            $reservation->setUser($currentUser);
        }

        $details = $request->request->get('details');
        $reservation->setDetails($details);
        $reservation->setStatus(true);
        $reservation->setCreatedAt(new \DateTimeImmutable());
        $reservation->setHeureDebut($startTime);
        $reservation->setHeureFin($endTime);
        
        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Le rendez-vous a bien était enregistré!');
    } else {
        $this->addFlash('error', 'Il y a eu un problème lors de l\'enregistrement du rendez-vous');
    }
    if (in_array("ROLE_ADMIN", $currentRole)){
        return $this->redirectToRoute('app_rdv_show');
    }
    else{
    return $this->redirectToRoute('confirmation_page');}
}

#[Route("/confirmation", name:"confirmation_page")]
public function confirmationPage()
{
   return $this->render('home/index.html.twig');
}

#[Route("/form-page", name:"form_page")]
public function formPage()
{
   return $this->render('rdv/_form.html.twig');
}

#[Route("/show", name:"app_rdv_show", methods:["GET"])]
    public function show(RdvRepository $repo): Response
    {
        $rdvs= $repo->findAll();


        $eventsData = [];
        foreach ($rdvs as $rdv) {
        $eventsData[] = [
        'title' => $rdv->getUser()->getName(). " " .$rdv->getUser()->getFirstName(),
        'start' => $rdv->getHeureDebut()->format('Y-m-d H:i:s'),
        'end' => $rdv->getHeureFin()->format('Y-m-d H:i:s'),
        'id' => $rdv->getId(), 
        ];
}
        return $this->render('rdv/show.html.twig',[
            'eventsJson' => json_encode($eventsData)
        ]);
    }



    #[Route('rdv/{id}/details', name: 'app_rdv_details', methods: ['GET'])]
    public function edit(Rdv $rdv): Response
    {

        return $this->render('rdv/details.html.twig', [
            'rdv' => $rdv
        ]);
    }

    #[Route("/my-appointments", name:"app_rdv_my_appointments", methods:["GET"])]
public function myAppointments(RdvRepository $repo): Response
{
    $user = $this->getUser();
    $rdvs = $repo->findByUser($user);

    return $this->render('rdv/index.html.twig', [
        'rdvs' => $rdvs
    ]);
}


    #[Route('rdv/{id}', name: 'app_rdv_delete', methods: ['POST'])]
    public function delete(Request $request, Rdv $rdv, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rdv->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rdv);
            $entityManager->flush();
        }

        $currentUser = $this->getUser();
        $currentRole = $currentUser->getRoles();

        $reservation = new Rdv();

        if (in_array("ROLE_ADMIN", $currentRole)) {
            return $this->redirectToRoute('app_rdv_show'); 
        } else {
            return $this->redirectToRoute('app_rdv_my_appointments');
        }

        
    }
}