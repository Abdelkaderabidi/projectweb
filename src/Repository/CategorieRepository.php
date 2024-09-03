<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

//    /**
//     * @return Categorie[] Returns an array of Categorie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Categorie
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function sortByNomCat($searchTerm = null)
{
    // Créer la requête query builder
    $queryBuilder = $this->createQueryBuilder('c');

    // Si un terme de recherche est spécifié, filtrer les résultats en fonction du nom de la catégorie
    if ($searchTerm) {
        // Séparer le terme de recherche en mots
        $words = explode(' ', $searchTerm);

        // Créer la condition WHERE
        $whereCondition = '';
        foreach ($words as $index => $word) {
            $parameterName = 'searchTerm' . $index;
            $whereCondition .= '(c.nomCat LIKE :' . $parameterName . ') AND ';
            $queryBuilder->setParameter($parameterName, '%' . $word . '%');
        }

        // Supprimer le "AND" supplémentaire à la fin de la condition WHERE
        $whereCondition = rtrim($whereCondition, ' AND ');

        // Ajouter la condition WHERE à la requête query builder
        $queryBuilder->andWhere($whereCondition);
    }

    // Trier les résultats par nom de catégorie
    $queryBuilder->orderBy('c.nomCat', 'ASC');

    // Exécuter la requête et récupérer les résultats
    return $queryBuilder->getQuery()->getResult();
}


public function searchByNomCat($searchTerm)
{
    return $this->createQueryBuilder('c')
        ->andWhere('c.nomCat LIKE :searchTerm')
        ->setParameter('searchTerm', '%' . $searchTerm . '%')
        ->getQuery()
        ->getResult();
}
}
