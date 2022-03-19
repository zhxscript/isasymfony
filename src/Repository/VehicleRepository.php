<?php

namespace App\Repository;

use App\Dto\Page;
use App\Entity\Vehicle;
use App\Dto\VehicleSummaryDto;
use App\Enums\VehicleTypeEnum;
use Doctrine\ORM\ORMException;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

/**
 * @method Vehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicle[]    findAll()
 * @method Vehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    public static function createIsDeletedCriteria(bool $deleted = false): Criteria
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('deleted', $deleted));
    }

    public static function createIsUsedCriteria(bool $used = true): Criteria
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('type', ($used) ? VehicleTypeEnum::USED->value: VehicleTypeEnum::NEW->value));
    }

    public function findByKeyword(string $q = null, array $sort = [], string $column = 'full', int $offset = 0, int $limit = 20, bool $isUsed = true, bool $isDeleted = false): Page
    {
        $query = $this->createQueryBuilder("p")
            ->addCriteria(self::createIsDeletedCriteria($isDeleted))
            ->addCriteria(self::createIsUsedCriteria($isUsed));

        if ($q) {
            if (strtolower($column) == 'full') {
                $query->andWhere("p.model like :q or p.make like :q")
                    ->setParameter('q', "%" . $q . "%");
            } else {
                $query->andWhere("p.{$column} like :q")
                    ->setParameter('q', "%" . $q . "%");
            }
        }

        if (sizeof($sort) == 2 && in_array(strtoupper($sort[1]), ['DESC', 'ASC'])) {
            $query->orderBy('p.' . $sort[0], strtoupper($sort[1]));
        } else {
            $query->orderBy('p.id', 'DESC');
        }

        $query->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $c = count($paginator);
        $content = new ArrayCollection();
        foreach ($paginator as $vehicle) {
            $content->add(VehicleSummaryDto::of(
                $vehicle->getId(),
                $vehicle->getModel(),
                $vehicle->getMake(),
                $vehicle->getType(),
                $vehicle->getMsrp(),
                $vehicle->getVin(),
                $vehicle->getMiles(),
                $vehicle->getDateAdded(),
            ));
        }
        return Page::of($content, $c, $offset, $limit);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Vehicle $entity, bool $flush = true): void
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
    public function remove(Vehicle $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function findById(int $id, bool $isUsed = true, bool $isDeleted = false): ?Vehicle
    {
        return $this->createQueryBuilder('v')
            ->addCriteria(self::createIsDeletedCriteria($isDeleted))
            ->addCriteria(self::createIsUsedCriteria($isUsed))
            ->andWhere('v.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
