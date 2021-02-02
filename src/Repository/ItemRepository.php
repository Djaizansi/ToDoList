<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    // /** SELECT * FROM item i INNER JOIN todo_list td ON i.todo_list_id = td.id ORDER BY created_at DESC LIMIT 1 */
    //  * @return Item[] Returns an array of Item objects
    //  */

    public function getLastItem($id)
    {
        return $this->createQueryBuilder('i')
            ->where('i.todoList = :id')
            ->orderBy('i.createdAt', 'DESC')
            ->setParameter('id',$id)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Item
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
