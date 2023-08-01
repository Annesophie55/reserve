<?php

namespace App\Controller;

use DateTime;
use App\Entity\Rdv;
use App\Form\TakeRdvType;
use App\Repository\RdvRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/rdv')]
class RdvController extends AbstractController
{
    #[Route('/', name: 'app_rdv_index', methods: ['GET'])]
    public function index(RdvRepository $rdvRepository): Response
    {
        return $this->render('rdv/index.html.twig', [
            'rdvs' => $rdvRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_rdv_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            // Récupère les données du formulaire HTML
            // Date
            // Heure
            $hourString = $request->request->get('hour');
    
            // Convertir la chaîne d'heure en objet DateTime
            $hour = DateTime::createFromFormat('H:i', $hourString);
    
            // Vérifier si la conversion a réussi
            if (!$hour) {
                throw new \InvalidArgumentException("Format d'heure invalide : $hourString");
            }
    
            // Crée un nouvel objet Rdv et définis les valeurs
            $rdv = new Rdv();
            $rdv->setDayHour($hour);
            $rdv->setDuration('01:30'); // Durée du rendez-vous est de 1H30
    
            // Enregistre le rendez-vous en base de données
            $entityManager->persist($rdv);
            $entityManager->flush();
    
            // Redirige l'utilisateur vers une page de confirmation, par exemple
            return $this->redirectToRoute('app_rdv_confirmation');
        }
    
        return $this->render('rdv/new.html.twig');
    }

    #[Route('/{id}', name: 'app_rdv_show', methods: ['GET'])]
    public function show(Rdv $rdv): Response
    {
        return $this->render('rdv/show.html.twig', [
            'rdv' => $rdv,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rdv_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rdv $rdv, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RdvType::class, $rdv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rdv_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rdv/edit.html.twig', [
            'rdv' => $rdv,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rdv_delete', methods: ['POST'])]
    public function delete(Request $request, Rdv $rdv, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rdv->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rdv);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rdv_index', [], Response::HTTP_SEE_OTHER);
    }
}
