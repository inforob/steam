<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository
{
    private const RECORDS_BY_PAGE = 10 ;
    private const RATING_VALUE_MIN = 80 ;
    private const RATING_VALUE_MAX = 100 ;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function add(Game $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Game $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function paginate(int $currentPage, $filter = null): array
    {
        $pageSize = self::RECORDS_BY_PAGE ;
        $query = $this->createQueryBuilder('g');

        if(!empty($filter)){

            if(array_key_exists('description',$filter)){
                $query->where($query->expr()->like('g.description', ':description'));
                $query->orWhere($query->expr()->like('g.title', ':description'));
                $query->setParameter('description', '%' . $filter['description'] . '%');
            }

            if(array_key_exists('price',$filter)){
                [$valueMin,$valueMax] = explode(",",$filter['price']);
                $query->andWhere($query->expr()->gte('g.price', ':valueMin'));
                $query->andWhere($query->expr()->lte('g.price', ':valueMax'));
                $query->setParameter('valueMin', $valueMin);
                $query->setParameter('valueMax', $valueMax);
            }

            if(array_key_exists('platform',$filter)){
                $query->andWhere($query->expr()->eq('g.platform', ':platform'));
                $query->setParameter('platform',  $filter['platform']);
            }
        }

        $query->andWhere($query->expr()->eq('g.published', Game::PUBLISHED));
        $query->orderBy('g.createdAt', 'ASC');
        $sql = $query->getQuery()->getSQL();
        $totalItems = count($query->getQuery()->getResult());
        $paginator  = new Paginator($query);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($currentPage-1)) // set the offset
            ->setMaxResults($pageSize); // set the limit

        return [
            'games'=> $paginator->getQuery()->getResult(),
            'recordsByPage'=> $pageSize,
            'total'=> $totalItems
        ];
    }

    public function getMostPopular(): array
    {
        $query = $this->createQueryBuilder('g');

        $query->andWhere($query->expr()->gte('g.rating', ':valueMin'));
        $query->andWhere($query->expr()->lte('g.rating', ':valueMax'));

        $query->setParameter('valueMin', self::RATING_VALUE_MIN);
        $query->setParameter('valueMax', self::RATING_VALUE_MAX);

        $query->orderBy('g.createdAt', 'ASC');
        $query->setMaxResults(6);

        return $query->getQuery()->getResult();
    }

}
