<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



public function searchByName($searchTerm)
{
    // Séparer le terme de recherche en mots
    $words = explode(' ', $searchTerm);

    // Créer la requête query builder
    $queryBuilder = $this->createQueryBuilder('p');

    // Créer la condition WHERE
    $whereCondition = '';
    foreach ($words as $index => $word) {
        $parameterName = 'searchTerm' . $index;
        $whereCondition .= '(p.nomProd LIKE :' . $parameterName . ') AND ';
        $queryBuilder->setParameter($parameterName, '%' . $word . '%');
    }

    // Supprimer le "AND" supplémentaire à la fin de la condition WHERE
    $whereCondition = rtrim($whereCondition, ' AND ');

    // Ajouter la condition WHERE à la requête query builder
    $queryBuilder->andWhere($whereCondition);

    // Exécuter la requête et récupérer les résultats
    return $queryBuilder->getQuery()->getResult();
}

public function sortByNomProd(): array
{
    return $this->createQueryBuilder('p')
        ->orderBy('p.nomProd', 'ASC')
        ->getQuery()
        ->getResult();
}

public function sortByPrice(): array
{
    return $this->createQueryBuilder('p')
        ->orderBy('p.prix', 'ASC')
        ->getQuery()
        ->getResult();
}
public function sortByQuantity(): array
{
    return $this->createQueryBuilder('p')
        ->orderBy('p.quantite', 'ASC')
        ->getQuery()
        ->getResult();
}


public function findByPrix($prix)
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.prix <= :prix')
        ->setParameter('prix', $prix)
        ->getQuery()
        ->getResult();
}

public function findByDescription($description)
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.description LIKE :description')
        ->setParameter('description', '%' . $description . '%')
        ->getQuery()
        ->getResult();
}

public function findByQuantite($quantite)
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.quantite = :quantite')
        ->setParameter('quantite', $quantite)
        ->getQuery()
        ->getResult();
}


}
