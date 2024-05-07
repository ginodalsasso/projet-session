<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\CreateStagiaireType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(StagiaireRepository $stagiaireRepository): Response
    {
        $stagiaires = $stagiaireRepository->findAll(); 

        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires
        ]);
    }

  
//--------------------------------------------------CREER/EDITER STAGIAIRE------------------------------------------------------
    // affichage et création d'un stagiaire
    
    #[IsGranted('ROLE_ADMIN')]
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
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/stagiaire/delete/{id}', name: 'app_stagiaire_delete', requirements: ['id' => '\d+'])]
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


}
