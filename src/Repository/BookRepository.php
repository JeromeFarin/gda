<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param string|null $s
     * @param string|null $dir
     * @param string|null $amount
     */
    public function findByFilter(?string $s = null, ?string $dir = null, ?string $amount = null)
    {
        $query = $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(5);

        if ($s) {
            $query
                ->andWhere('b.title LIKE :s')
                ->orWhere('b.isbn LIKE :s')
                ->setParameter('s', '%' . $s . '%');
        }

        if ($dir && $amount) {
            $query
                ->andWhere("b.price $dir :amount")
                ->setParameter(':amount', $amount);
        }

        return $query->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
     */
}
