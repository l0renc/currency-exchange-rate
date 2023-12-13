<?php

namespace App\Repository;

use App\Entity\CurrencyRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrencyRate>
 *
 * @method CurrencyRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyRate[]    findAll()
 * @method CurrencyRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRate::class);
    }

    public function findByBaseAndTargetCurrencies(string $baseCurrency, array $targetCurrencies): array
    {
        // Example query, adjust as needed for your entity structure
        return $this->createQueryBuilder('cr')
            ->where('cr.baseCurrency = :baseCurrency')
            ->andWhere('cr.targetCurrency IN (:targetCurrencies)')
            ->setParameter('baseCurrency', $baseCurrency)
            ->setParameter('targetCurrencies', $targetCurrencies)
            ->getQuery()
            ->getResult();
    }

    public function findRate(string $baseCurrency, string $targetCurrency): ?float
    {
        $result = $this->createQueryBuilder('cr')
            ->select('cr.rate')
            ->where('cr.baseCurrency = :baseCurrency')
            ->andWhere('cr.targetCurrency = :targetCurrency')
            ->setParameter('baseCurrency', $baseCurrency)
            ->setParameter('targetCurrency', $targetCurrency)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ? $result['rate'] : null;
    }

//    /**
//     * @return CurrencyRate[] Returns an array of CurrencyRate objects
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

//    public function findOneBySomeField($value): ?CurrencyRate
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
