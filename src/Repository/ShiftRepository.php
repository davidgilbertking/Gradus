<?php

namespace App\Repository;

use App\Entity\Shift;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\DeploymentStatus;

/**
 * @extends ServiceEntityRepository<Shift>
 */
class ShiftRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Shift::class);
	}
	//    /**
	//     * @return Shift[] Returns an array of Shift objects
	//     */
	//    public function findByExampleField($value): array
	//    {
	//        return $this->createQueryBuilder('s')
	//            ->andWhere('s.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->orderBy('s.id', 'ASC')
	//            ->setMaxResults(10)
	//            ->getQuery()
	//            ->getResult()
	//        ;
	//    }

	//    public function findOneBySomeField($value): ?Shift
	//    {
	//        return $this->createQueryBuilder('s')
	//            ->andWhere('s.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->getQuery()
	//            ->getOneOrNullResult()
	//        ;
	//    }

    public function findByDeploymentStatusSorted(DeploymentStatus $status, string $direction = 'ASC'): array
    {
        return $this->createQueryBuilder('s')
                    ->join('s.deployment', 'd')
                    ->andWhere('d.status = :status')
                    ->setParameter('status', $status->value)
                    ->orderBy('s.date', $direction === 'DESC' ? 'DESC' : 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}
