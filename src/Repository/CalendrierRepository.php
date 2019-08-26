<?php /**
 * Copyright (C) 8 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
 * @file /Users/davidannebicque/htdocs/intranetv3/src/Repository/CalendrierRepository.php
 * @author     David Annebicque
 * @project intranetv3
 * @date 26/08/2019 13:45
 * @lastUpdate 26/08/2019 13:13
 */ /** @noinspection ALL */
/** @noinspection PhpUnused */

namespace App\Repository;

use App\Entity\AnneeUniversitaire;
use App\Entity\Calendrier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Calendrier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calendrier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calendrier[]    findAll()
 * @method Calendrier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendrierRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Calendrier::class);
    }

    public function findByAnneeUniversitaire(AnneeUniversitaire $anneeUniversitaire)
    {
        return $this->createQueryBuilder('c')
            ->where('c.anneeUniversitaire = :annee')
            ->setParameter('annee', $anneeUniversitaire->getId())
            ->getQuery()
            ->getResult();
    }

    public function findCalendrierArray(): array
    {
        $all = $this->findAll();

        $t = [];

        /** @var  $a Calendrier */
        foreach ($all as $a) {
            $t[$a->getSemaineFormation()]['lundi'] = $a->getDatelundi();
            $t[$a->getSemaineFormation()]['semainereelle'] = $a->getSemaineReelle();
        }

        return $t;
    }
}
