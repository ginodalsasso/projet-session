<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function StagiairesNonInscrits($session_id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
 
       
        // sélectionner tous les stagiaires d'une session dont l'id est passé en paramètre
        $qb->select('s')
            ->from('App\Entity\Stagiaire', 's')
            ->leftJoin('s.sessions', 'se')
            ->where('se.id = :id');
       
        $sub = $em->createQueryBuilder();
        // sélectionner tous les stagiaires qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les stagiaires non inscrits pour une session définie
        $sub->select('st')
            ->from('App\Entity\Stagiaire', 'st')
            ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
            // requête paramétrée
            ->setParameter('id', $session_id)
            // trier la liste des stagiaires sur le nom de famille
            ->orderBy('st.nom');
       
        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
        //requête DQL équivaut en SQL:
        //SELECT * FROM stagiaire
        //WHERE id_stagiaire NOT IN (SELECT id_stagiaire FROM participer WHERE id_session = : id_session)
        //où participer serait la table associative entre SESSION et STAGIAIRE 
    }


    public function ModulesNonInscrits($module_id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
       
        // sélectionner tous les modules d'une session dont l'id est passé en paramètre
        $qb->select('m')
            ->from('App\Entity\Module', 'm')
            ->leftJoin('m.programmes', 'pr')
            ->where('pr.id = :id');

        $sub = $em->createQueryBuilder();
        // sélectionner tous les modules qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les modules non inscrits pour une session définie
        $sub->select('mo')
            ->from('App\Entity\module', 'mo')
            ->where($sub->expr()->notIn('mo.id', $qb->getDQL()))
            // requête paramétrée
            ->setParameter('id', $module_id)
            // trier la liste des stagiaires sur le nom de famille
            ->orderBy('mo.nomModule');
       
        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();

        // SELECT * 
        // FROM module m
        // LEFT JOIN programme p ON m.id = p.module_id
        // WHERE m.id NOT IN (SELECT module_id FROM programme WHERE session_id = 3)

       
    }
}
