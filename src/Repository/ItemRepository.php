<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\User;
use Doctrine\ORM\Query;
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
    /**
     * ItemRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function findItemsByUser(User $user)
    {
        $builder = $this->createQueryBuilder('i');
        $builder
            ->select('i.id')
            ->addSelect('i.data')
            ->addSelect('i.createdAt as created_at')
            ->addSelect('i.updatedAt as updated_at')
            ->where('i.user = :user')
            ->setParameter('user', $user);

        // return the data array without converting it into an object
        return $builder->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
