<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\CreateSessionType;
use App\Repository\ModuleRepository;
use App\Repository\SessionRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InterfaceController extends AbstractController
{
    #[Route('/interface', name: 'app_interface')]
    public function index(): Response
    {
        return $this->render('interface/index.html.twig', [
            'controller_name' => 'InterfaceController',
        ]);
    }

    // affichage de la liste des formations
    #[Route('/formation', name: 'app_formation')]
    public function listFormations(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll(); 

        return $this->render('formation/index.html.twig', [
            'formations' => $formations
        ]);
    }

    // affichage de la liste des sessions
    #[Route('/session', name: 'app_session')]
    public function listSessions(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findAll(); 

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions
        ]);
    }

    // affichage de la liste des modules
    #[Route('/module', name: 'app_module')]
    public function listModules(ModuleRepository $moduleRepository): Response
    {
        $modules = $moduleRepository->findAll(); 
        
        return $this->render('module/index.html.twig', [
            'modules' => $modules
        ]);
    }
    
    // affichage et création d'une session
    #[Route('/addSession', name: 'app_addSession')]
    public function addSession(EntityManagerInterface $entityManager, Request $request): Response
    {
        $newSession = new Session();
        $form = $this->createForm(CreateSessionType::class, $newSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entityManager->persist($newSession);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            return $this->redirectToRoute('app_session');
        }

        return $this->render('addSession/index.html.twig', [
            'form' => $form
        ]);
    }
}
