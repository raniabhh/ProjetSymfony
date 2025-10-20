<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function getNbrBooksDQL(): int
    {
        $dql = 'SELECT COUNT(b.id) FROM App\Entity\Book b';
        return $this->getEntityManager()
            ->createQuery($dql)
            ->getSingleScalarResult();
    }

    public function getNbrBooksQB(): int
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getBooksByAuthorDQL($authorId): array
    {
        $dql = 'SELECT b FROM App\Entity\Book b WHERE b.author = :authorId';
        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('authorId', $authorId)
            ->getResult();
    }

    public function getBooksByAuthorQB($authorId): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.author = :authorId')
            ->setParameter('authorId', $authorId)
            ->getQuery()
            ->getResult();
    }
}
