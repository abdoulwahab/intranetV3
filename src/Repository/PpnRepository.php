<?php

namespace App\Repository;

use App\Entity\Diplome;
use App\Entity\Ppn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Ppn|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ppn|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ppn[]    findAll()
 * @method Ppn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PpnRepository extends ServiceEntityRepository
{
    /**
     * PpnRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ppn::class);
    }

    /**
     * @param Diplome $diplome
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByDiplomeBuilder(Diplome $diplome): \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.diplome = :diplome')
            ->setParameter('diplome', $diplome->getId());
    }
}
