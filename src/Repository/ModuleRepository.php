<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 *
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    //    /**
    //     * @return Module[] Returns an array of Module objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Module
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function ModulesNonInscrits($session_id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
       
        // sélectionner tous les modules d'une session dont l'id est passé en paramètre
        $qb->select('m')
            ->from('App\Entity\Module', 'm')
            ->leftJoin('m.programmes', 'mp')
            ->where('mp.session = :id');

        $sub = $em->createQueryBuilder();
        // sélectionner tous les modules qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les modules non inscrits pour une session définie
        $sub->select('mt')
            ->from('App\Entity\Module', 'mt')
            ->where($sub->expr()->notIn('mt.id', $qb->getDQL()))
            // requête paramétrée de l'id récupéré précédemment
            ->setParameter('id', $session_id);
            // trier la liste des stagiaires sur le nom de famille
            // ->orderBy('mo.nomModule');
       
        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();

        // SELECT * 
        // FROM module m
        // LEFT JOIN programme p ON m.id = p.module_id
        // WHERE m.id NOT IN (SELECT module_id FROM programme WHERE session_id = 3)
       
    }
}
