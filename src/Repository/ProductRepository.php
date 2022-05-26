<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Array_;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Requête qui permet de récupérer les produits en fonction de la recherche de  l'utilisateur
     * @param Search $search
     * @return Product
     */
    public function findWithSearch(Search $search) : array {
        //Ici on crée la requête de base
         $query = $this
                     ->createQueryBuilder('p') // La requête sera mapper avec la table Product 'p'
                     ->select('c','p') //On seléctionne une category et un produit
                     ->join('p.category','c'); //On fait une jointure entre la colonne categories de la table produit et la table category

          //Ici on text si il ya des checkbox qui ont été checkées
          if(!empty($search->categories)){
              $query = $query
                          ->andWhere('c.id IN (:categories)') //Ici on indique que l'on veut filtrer par les id'c.id dans la liste categories envoyer en paramètre'
                          ->setParameter('categories',$search->categories);
          }

          //Ici on test si un texte a été saisie dans le champ input
          if(!empty($search->string)){ //Ici on va utiliser search->string qui représente le texte saisie dans le champ de recherche
             $query = $query
                          ->andWhere('p.name LIKE :string')
                          ->setParameter('string', "%{$search->string}%"); //Ici le procédé est le même que pour une requête like en sql
          }
          return $query->getQuery()->getResult(); //On retourne la requête ainsi que ses résultats
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Product $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Product $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
