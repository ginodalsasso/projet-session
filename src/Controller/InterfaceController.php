<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\CreateSessionType;
use App\Repository\ModuleRepository;
use App\Repository\SessionRepository;
use App\Repository\FormationRepository;
use App\Repository\ProgrammeRepository;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InterfaceController extends AbstractController
{
    // affichage de la gestion d'une session
    #[Route('/interface', name: 'app_interface')]
    public function index(FormationRepository $formationRepository, SessionRepository $sessionRepository, ModuleRepository $moduleRepository, StagiaireRepository $stagiaireRepository): Response
    {
        $formations = $formationRepository->findAll(); 
        $sessions = $sessionRepository->findAll(); 
        $modules = $moduleRepository->findAll(); 
        $stagiaires = $stagiaireRepository->findAll(); 
        return $this->render('interface/index.html.twig', [
            'formations' => $formations,
            'sessions' => $sessions,
            'modules' => $modules,
            'stagiaires' => $stagiaires
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
    public function listSessions(SessionRepository $sessionRepository, ModuleRepository $moduleRepository, ProgrammeRepository $programmeRepository): Response
    {
        $sessions = $sessionRepository->findAll(); 
        // $programmes = $programmeRepository->findAll(); 
        // $modules = $moduleRepository->findAll(); 

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
            // 'programmes' => $programmes,
            // 'modules' => $modules
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
        // Crée une nouvelle instance de Session
        $newSession = new Session();
        // Crée un formulaire pour la nouvelle session en utilisant le formulaire CreateSessionType
        $form = $this->createForm(CreateSessionType::class, $newSession);
        // Gère la soumission du formulaire
        $form->handleRequest($request);
        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid())
        {
            // Persiste la nouvelle session pour la sauvegarder dans la base de données
            $entityManager->persist($newSession);

            // Exécute les requêtes pour enregistrer la nouvelle session dans la base de données (INSERT)           
            $entityManager->flush();
            // Redirige vers la route 'app_session'
            return $this->redirectToRoute('app_session');
        }

        return $this->render('addSession/index.html.twig', [
            'form' => $form
        ]);
    }
}
