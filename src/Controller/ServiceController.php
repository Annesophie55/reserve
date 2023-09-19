<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\CommentRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/service')]
class ServiceController extends AbstractController
{
    #[Route('/', name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        $services = $serviceRepository->findAll();
        $currentUser = $this->getUser();
        if($currentUser){
        $currentRole = $currentUser->getRoles();

        if(in_array("ROLE_ADMIN", $currentRole)){
            return $this->render('service/index.html.twig', [
                'services' => $services,
            ]);  
        }
        else{
        return $this->render('service/list.html.twig', [
            'services' => $services,
        ]);}
        }
        else{
            return $this->render('service/list.html.twig', [
                'services' => $services,
            ]);}
    }

    #[Route('/services/json', name: 'app_services_json')]
    public function getServicesJson(ServiceRepository $serviceRepository): JsonResponse
    {
        $services = $serviceRepository->findAll();
        $serviceData = [];
    
        foreach ($services as $service) {
            $serviceData[] = [
                'id' => $service->getId(),
                'name' => $service->getName(),
                'url' => $this->generateUrl('app_service_show', ['id' => $service->getId()]),
            ];
        }
    
        return $this->json(['services' => $serviceData]);
    }
    


    #[Route('/new', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service/new.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findLatest(5);

        return $this->render('service/show.html.twig', [
            'service' => $service,
            'comments' => $comments,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_service_index');
    }
}
