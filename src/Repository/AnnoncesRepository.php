<?php

namespace App\Repository;

use App\Entity\Annonces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonces|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonces|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonces[]    findAll()
 * @method Annonces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnoncesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonces::class);
    }

    /**
     * Recherche les annonces en fonction du formulaire
     * @return void
     */
    public function search($mots = null, $categories = null){
        $query = $this->createQueryBuilder('a');
        $query->where('a.active = 1');
        if($mots != null){
            $query->andWhere('MATCH_AGAINST(a.title, a.content) AGAINST (:mots boolean)>0')
                ->setParameter('mots', $mots);
        }
        if($categories != null){
            $query->join('a.categories', 'c');
            $query->andWhere('c.id = :id');
            $query->setParameter('id', $categories );
        }



        return $query->getQuery()->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Annonces
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
