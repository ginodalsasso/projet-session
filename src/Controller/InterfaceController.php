<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Stagiaire;
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

//--------------------------------------------------AFFICHAGE------------------------------------------------------
    // affichage de la gestion d'une session
    #[Route('/interface/{id}', name: 'app_interface')]
    public function index(int $id, Session $session = null, SessionRepository $sessionRepository): Response
    {
        // Vérifie si l'entité Session a été trouvée
        if (!$session) {
            throw $this->createNotFoundException(
                'id non trouvé '.$id
            );
        }
        $nonInscrits = $sessionRepository->StagiairesNonInscrits($session->getId());
        $moduleNonInscrits = $sessionRepository->ModulesNonInscrits($session->getId());

        return $this->render('interface/index.html.twig', [
            'session' => $session,
            'nonInscrits' => $nonInscrits,
            'moduleNonInscrits' => $moduleNonInscrits
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
    
//--------------------------------------------------CREER/EDITER SESSION------------------------------------------------------
    // affichage et création d'une session
    #[Route('/addSession', name: 'app_addSession')]
    #[Route('/{id}/editSession', name: 'app_editSession')]
    public function add_editSession(Session $session = null, EntityManagerInterface $entityManager, Request $request): Response
    {   
        //si la session n'existe pas alors
        if(!$session){
            // Crée une nouvelle instance de Session
            $session = new Session();
        }
        // Crée un formulaire pour la nouvelle session en utilisant le formulaire CreateSessionType
        $form = $this->createForm(CreateSessionType::class, $session);
        // Gère la soumission du formulaire
        $form->handleRequest($request);
        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid())
        {
            // Persiste la nouvelle session pour la sauvegarder dans la base de données
            $entityManager->persist($session);

            // Exécute les requêtes pour enregistrer la nouvelle session dans la base de données (INSERT)           
            $entityManager->flush();
            // Redirige vers la route 'app_session'
            return $this->redirectToRoute('app_session');
        }
        return $this->render('addSession/index.html.twig', [
            'form' => $form
        ]);
    }

//--------------------------------------------------SUPPRIMER SESSION ------------------------------------------------------
    // Suppression d'une session
    #[Route('/session/delete/{id}', name: 'app_session_delete')]
    public function deleteSession(EntityManagerInterface $entityManager, int $id): Response
    {
        $session = $entityManager->getRepository(Session::class)->find($id); 
        // Vérifie si l'entité Session a été trouvée
        if (!$session) {
            throw $this->createNotFoundException(
                'id non trouvé '.$id
            );
        }
        //remove notifie à doctrine que nous cherchons à suprimer un élément    
        $entityManager->remove($session);
        //la supression ne prend effect qu'avec flush
        $entityManager->flush();
        // Redirige vers la route 'app_session'
        $this->addFlash('success', 'La session a bien été suprimée');

        return $this->redirectToRoute('app_session'); 
    }


//--------------------------------------------------AJOUTER / SUPPRIMER UN STAGIAIRE EN SESSION ------------------------------------------------------

    #[Route('/interface/{sessionId}/{stagiaireId}/addStagiaireToSession', name: 'addStagiaire')]
    public function addStagiaireToSession(EntityManagerInterface $entityManager, int $sessionId, int $stagiaireId): Response
    {    
        // Recherche la session par son id
        $session = $entityManager->getRepository(Session::class)->find($sessionId);  

        // Vérifie si l'entité Session a été trouvée
        if (!$session) {
            throw $this->createNotFoundException(
                'Session non trouvée pour l\'id '.$sessionId
            );
        }
        
        // Recherche le stagiaire par son id
        $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);

        // Vérifie si l'entité Stagiaire a été trouvée
        if (!$stagiaire) {
            throw $this->createNotFoundException('
                Stagiaire non trouvé pour l\'id '.$stagiaireId
            );
        }
        // Vérifie si le stagiaire est déjà inscrit dans la session
        if ($session->getStagiaires()->contains($stagiaire)) {
            return new Response(
                'Le stagiaire est déjà inscrit dans cette session');
        }

        // addStagiaire est une methode provenant de l'entité stagiaire 
        $session->addStagiaire($stagiaire);
        // Persiste les modifications dans la base de données
        $entityManager->flush();

        // Redirige vers la route 'interface' avec l'ID de la session
        return $this->redirectToRoute('app_interface', ['id' => $session->getId()]);
    }

//----------------------------------------------------------------------------------------------------------------

    // Suppression d'un stagiaire en session
    #[Route('/interface/{sessionId}/{stagiaireId}/removeStagiaireToSession', name: 'deleteStagiaire')]
    public function removeStagiaireToSession(EntityManagerInterface $entityManager, int $sessionId, int $stagiaireId): Response
    {
        // Recherche la session par son id
        $session = $entityManager->getRepository(Session::class)->find($sessionId);  

        // Vérifie si l'entité Session a été trouvée
        if (!$session) {
            throw $this->createNotFoundException(
                'Session non trouvée pour l\'id '.$sessionId
            );
        }
        
        // Recherche le stagiaire par son id
        $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);

        // Vérifie si l'entité Stagiaire a été trouvée
        if (!$stagiaire) {
            throw $this->createNotFoundException('
                Stagiaire non trouvé pour l\'id '.$stagiaireId
            );
        }
        // Vérifie si le stagiaire est déjà desinscrit dans la session
        if (!$session->getStagiaires()->contains($stagiaire)) {
            return new Response(
                'Le stagiaire est déjà désinscrit de cette session');
        }
        
        //remove notifie à doctrine que nous cherchons à suprimer un élément    
        $session->removeStagiaire($stagiaire);
        //la supression ne prend effect qu'avec flush
        $entityManager->flush();

        // Redirige vers la route 'interface' avec l'ID de la session
        return $this->redirectToRoute('app_interface', ['id' => $session->getId()]);
    }
}
