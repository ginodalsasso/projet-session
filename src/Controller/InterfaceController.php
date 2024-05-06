<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Formation;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\CreateModuleType;
use App\Form\CreateSessionType;
use Doctrine\ORM\EntityManager;
use App\Form\CreateFormationType;
use App\Form\CreateStagiaireType;
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
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;

class InterfaceController extends AbstractController
{

//--------------------------------------------------AFFICHAGE------------------------------------------------------
    // affichage de la gestion d'une session
    #[Route('/interface/{id}', name: 'app_interface', requirements: ['id' => '\d+'])]
    public function index(int $id, Session $session = null, SessionRepository $sessionRepository, ModuleRepository $moduleRepository): Response
    {
        // Vérifie si l'entité Session a été trouvée
        if (!$session) {
            throw $this->createNotFoundException(
                'id non trouvé '.$id
            );
        }
        $nonInscrits = $sessionRepository->StagiairesNonInscrits($session->getId());
        $moduleNonInscrits = $moduleRepository->ModulesNonInscrits($session->getId());

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



    
//--------------------------------------------------CREER/EDITER STAGIAIRE------------------------------------------------------
    // affichage et création d'un stagiaire
    #[Route('/editStagiaire', name: 'app_addStagiaire')]
    #[Route('stagiaire/{id}/editStagiaire', name: 'app_editStagiaire', methods: ['GET', 'POST'],  requirements: ['id' => '\d+'])]
    public function add_editStagiaire(Stagiaire $stagiaire = null, EntityManagerInterface $entityManager, Request $request): Response
    {   
        //si le stagiaire n'existe pas alors
        if(!$stagiaire){
            // Crée une nouvelle instance de stagiaire
            $stagiaire = new Stagiaire();
        }
        // Crée un formulaire pour le nouveau stagiaire en utilisant le formulaire CreateStagiaireType
        $form = $this->createForm(CreateStagiaireType::class, $stagiaire);
        // Gère la soumission du formulaire
        $form->handleRequest($request);
        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid())
        {
            // Persiste le nouveau stagiaire pour le sauvegarder dans la base de données
            $entityManager->persist($stagiaire);

            // Exécute les requêtes pour enregistrer le nouveau stagiaire dans la base de données (INSERT)           
            $entityManager->flush();

            $this->addFlash('success', 'Le stagiaire à été ajouté');
            // Redirige vers la route 'app_stagiaire
            return $this->redirectToRoute('app_stagiaire');
        }
        return $this->render('stagiaire/edit.html.twig', [
            'form' => $form
        ]);
    }
    //--------------------------------------------------SUPPRIMER STAGIAIRE ------------------------------------------------------
    // Suppression d'un stagiaire
    #[Route('/stagiaire/delete/{id}', name: 'app_stagiaire_delete',  methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function deleteStagiaire(EntityManagerInterface $entityManager, int $id): Response
    {
        $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($id); 
        // Vérifie si l'entité stagiaire a été trouvée
        if (!$stagiaire) {
            throw $this->createNotFoundException(
                'id non trouvé '.$id
            );
        }
        //remove notifie à doctrine que nous cherchons à suprimer un élément    
        $entityManager->remove($stagiaire);
        //la supression ne prend effect qu'avec flush
        $entityManager->flush();
        // Redirige vers la route 'app_stagiaire'

        $this->addFlash('success', 'Le stagiaire a bien été suprimé');

        return $this->redirectToRoute('app_stagiaire'); 
    }


//--------------------------------------------------CREER/EDITER SESSION------------------------------------------------------
    // affichage et création d'une session
    #[Route('/editSession', name: 'app_addSession')]
    #[Route('/session/{id}/editSession', name: 'app_editSession', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
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
        return $this->render('session/edit.html.twig', [
            'form' => $form
        ]);
    }

//--------------------------------------------------SUPPRIMER SESSION ------------------------------------------------------
    // Suppression d'une session
    #[Route('/session/delete/{id}', name: 'app_session_delete',  requirements: ['id' => '\d+'])]
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
    

//--------------------------------------------------CREER/EDITER MODULE------------------------------------------------------
    // affichage et création d'un module
    #[Route('/editModule', name: 'app_addModule')]
    #[Route('/module/{id}/editModule', name: 'app_editModule', methods: ['GET', 'POST'],  requirements: ['id' => '\d+'])]
    public function add_editModule(Module $module = null, EntityManagerInterface $entityManager, Request $request): Response
    {   
        //si le module n'existe pas alors
        if(!$module){
            // Crée une nouvelle instance de Module
            $module = new Module();
        }
        // Crée un formulaire pour le module en utilisant le formulaire CreateSessionType
        $form = $this->createForm(CreateModuleType::class, $module);
        // Gère la soumission du formulaire
        $form->handleRequest($request);
        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid())
        {
            // Persiste le nouveau module pour le sauvegarder dans la base de données
            $entityManager->persist($module);

            // Exécute les requêtes pour enregistrer le nouveau module dans la base de données (INSERT)           
            $entityManager->flush();
            // Redirige vers la route 'app_module'
            return $this->redirectToRoute('app_module');
        }
        return $this->render('module/edit.html.twig', [
            'form' => $form
        ]);
    }


//--------------------------------------------------SUPPRIMER MODULE ------------------------------------------------------
    // Suppression d'une module
    #[Route('/module/delete/{id}', name: 'app_module_delete',  requirements: ['id' => '\d+'])]
    public function deleteModule(EntityManagerInterface $entityManager, int $id): Response
    {
        $module = $entityManager->getRepository(Module::class)->find($id); 
        // Vérifie si l'entité module a été trouvée
        if (!$module) {
            throw $this->createNotFoundException(
                'id non trouvé '.$id
            );
        }
        //remove notifie à doctrine que nous cherchons à suprimer un élément    
        $entityManager->remove($module);
        //la supression ne prend effect qu'avec flush
        $entityManager->flush();
        // Redirige vers la route 'app_module'
        $this->addFlash('success', 'Le module a bien été suprimé');

        return $this->redirectToRoute('app_module'); 
    }


//--------------------------------------------------CREER/EDITER FORMATION------------------------------------------------------
    // affichage et création d'une formation
    #[Route('/editFormation', name: 'app_addFormation')]
    #[Route('/formation/{id}/editFormation', name: 'app_editFormation', methods: ['GET', 'POST'],  requirements: ['id' => '\d+'])]
    public function add_editFormation(Formation $formation = null, EntityManagerInterface $entityManager, Request $request): Response
    {   
        //si la formation n'existe pas alors
        if(!$formation){
            // Crée une nouvelle instance de formation
            $formation = new Formation();
        }
        // Crée un formulaire pour la formation en utilisant le formulaire CreateSessionType
        $form = $this->createForm(CreateFormationType::class, $formation);
        // Gère la soumission du formulaire
        $form->handleRequest($request);
        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid())
        {
            // Persiste la nouvelle formation pour le sauvegarder dans la base de données
            $entityManager->persist($formation);

            // Exécute les requêtes pour enregistrer la nouvelle formation dans la base de données (INSERT)           
            $entityManager->flush();
            // Redirige vers la route 'app_formation'
            return $this->redirectToRoute('app_formation');
        }
        return $this->render('formation/edit.html.twig', [
            'form' => $form
        ]);
    }

//--------------------------------------------------SUPPRIMER FORMATION ------------------------------------------------------
    // Suppression d'une formation
    #[Route('/formation/delete/{id}', name: 'app_formation_delete', requirements: ['id' => '\d+'])]
    public function deleteFormation(EntityManagerInterface $entityManager, int $id): Response
    {
        $formation = $entityManager->getRepository(Formation::class)->find($id); 
        // Vérifie si l'entité formation a été trouvée
        if (!$formation) {
            throw $this->createNotFoundException(
                'id non trouvé '.$id
            );
        }
        //remove notifie à doctrine que nous cherchons à suprimer un élément    
        $entityManager->remove($formation);
        //la supression ne prend effect qu'avec flush
        $entityManager->flush();
        // Redirige vers la route 'app_module'
        $this->addFlash('success', 'La formation a bien été suprimée');

        return $this->redirectToRoute('app_formation'); 
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


//--------------------------------------------------AJOUTER / SUPPRIMER UN MODULE EN SESSION ------------------------------------------------------

    #[Route('/interface/{sessionId}/{moduleId}/addProgrammeToSession', name: 'addProgramme')]
    public function addProgrammeToSession(ModuleRepository $moduleRepository, SessionRepository $sessionRepository, EntityManagerInterface $entityManager, int $sessionId, int $moduleId): Response
    {    

        // Recherche la session par son id
        $session = $sessionRepository->findOneById($sessionId);  
        $module = $moduleRepository->findOneById($moduleId);  

        // Vérifie si l'id Session a été trouvé
        if (!$session) {
            throw $this->createNotFoundException(
                'Session non trouvée pour l\'id '.$sessionId
            );
        }
        // Vérifie si l'id module a été trouvé
        if (!$module) {
            throw $this->createNotFoundException('
                Module non trouvé pour l\'id '.$moduleId
            );
        }
        
        $programme = new Programme();
        $programme->setDuree(1);
        $programme->setModule($module);
        $programme->setSession($session);

        $entityManager->persist($programme);
        // Persiste les modifications dans la base de données
        $entityManager->flush();

        // Redirige vers la route 'interface' avec l'ID de la session
        return $this->redirectToRoute('app_interface', ['id' => $session->getId()]);

    }

//----------------------------------------------------------------------------------------------------------------

   // Suppression d'un module en session
   #[Route('/interface/{sessionId}/{programmeId}/removeProgrammeToSession', name: 'deleteModule')]
   public function removeModuleToSession(SessionRepository $sessionRepository, EntityManagerInterface $entityManager, int $sessionId, int $programmeId): Response
   {
        // Recherche la session par son id
        $session = $sessionRepository->findOneById($sessionId);  
        // Recherche le module par son id
        $programme = $entityManager->getRepository(Programme::class)->find($programmeId);

       // Vérifie si l'entité programme a été trouvée
       if (!$programme) {
           throw $this->createNotFoundException('
               Programme non trouvé pour l\'id '.$programmeId
           );
       }
       
       //remove notifie à doctrine que nous cherchons à suprimer un élément    
        $delete = $session->removeProgramme($programme);

       $entityManager->persist($delete);
       //la supression ne prend effect qu'avec flush
       $entityManager->flush();

       // Redirige vers la route 'interface' avec l'ID de la session
       return $this->redirectToRoute('app_interface', ['id' => $session->getId()]);
   }
}
