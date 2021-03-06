<?php /**
 * Copyright (C) 8 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
 * @file /Users/davidannebicque/htdocs/intranetv3/src/Repository/AlternanceRepository.php
 * @author     David Annebicque
 * @project intranetv3
 * @date 26/08/2019 13:45
 * @lastUpdate 26/08/2019 13:13
 */ /**
 * Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
 * @file /Users/davidannebicque/htdocs/intranetv3/src/Repository/AlternanceRepository.php
 * @author     David Annebicque
 * @project intranetv3
 * @date 14/11/2019 14:57
 * @lastUpdate 14/11/2019 14:56
 */ // Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/Repository/AlternanceRepository.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14
/** @noinspection PhpUnused */

namespace App\Repository;

use App\Entity\Alternance;
use App\Entity\Annee;
use App\Entity\AnneeUniversitaire;
use App\Entity\Etudiant;
use App\Entity\Personnel;
use App\Entity\Semestre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Alternance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alternance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alternance[]    findAll()
 * @method Alternance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlternanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alternance::class);
    }

    public function findByPersonnelAnnee(Personnel $personnel, AnneeUniversitaire $anneeUniversitaire)
    {
        return $this->createQueryBuilder('a')
            ->where('a.anneeUniversitaire = :annee')
            ->andWhere('a.tuteurUniversitaire = :personnel')
            ->setParameter('annee', $anneeUniversitaire->getId())
            ->setParameter('personnel', $personnel->getId())
            ->getQuery()
            ->getResult();
    }

    public function findByPersonnelHistorique(Personnel $personnel, AnneeUniversitaire $anneeUniversitaire)
    {
        return $this->createQueryBuilder('a')
            ->where('a.anneeUniversitaire != :annee')
            ->andWhere('a.tuteurUniversitaire = :personnel')
            ->setParameter('annee', $anneeUniversitaire->getId())
            ->setParameter('personnel', $personnel->getId())
            ->getQuery()
            ->getResult();
    }

    public function findByEtudiantAnnee(Etudiant $etudiant, AnneeUniversitaire $anneeUniversitaire)
    {
        return $this->createQueryBuilder('a')
            ->where('a.anneeUniversitaire = :annee')
            ->andWhere('a.etudiant = :etudiant')
            ->setParameter('annee', $anneeUniversitaire->getId())
            ->setParameter('etudiant', $etudiant->getId())
            ->getQuery()
            ->getResult();
    }

    public function findByEtudiantHistorique(Etudiant $etudiant, AnneeUniversitaire $anneeUniversitaire)
    {
        return $this->createQueryBuilder('a')
            ->where('a.anneeUniversitaire != :annee')
            ->andWhere('a.tuteurUniversitaire = :etudiant')
            ->setParameter('annee', $anneeUniversitaire->getId())
            ->setParameter('etudiant', $etudiant->getId())
            ->getQuery()
            ->getResult();
    }

    public function findByAnnee(Annee $annee, AnneeUniversitaire $anneeUniversitaire)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin(Etudiant::class, 'e', 'WITH', 'a.etudiant = e.id')
            ->innerJoin(Semestre::class, 's', 'WITH', 's.id = e.semestre')
            ->where('a.anneeUniversitaire = :anneeuniversitaire')
            ->andWhere('s.annee = :annee')
            ->setParameter('anneeuniversitaire', $anneeUniversitaire->getId())
            ->setParameter('annee', $annee->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Annee              $annee
     * @param AnneeUniversitaire $anneeUniversitaire
     *
     * @return array
     */
    public function findByAnneeArray(Annee $annee, AnneeUniversitaire $anneeUniversitaire): array
    {
        $alternances = $this->findByAnnee($annee, $anneeUniversitaire);

        $tab = array();
        /** @var Alternance $alternance */
        foreach ($alternances as $alternance) {
            if ($alternance->getEtudiant() !== null) {
                $tab[$alternance->getEtudiant()->getId()] = $alternance;
            }
        }

        return $tab;
    }

    public function findOneByEtudiantAnnee(Etudiant $etudiant, AnneeUniversitaire $anneeUniversitaire)
    {
        return $this->createQueryBuilder('a')
            ->where('a.anneeUniversitaire = :annee')
            ->andWhere('a.etudiant = :etudiant')
            ->setParameter('annee', $anneeUniversitaire->getId())
            ->setParameter('etudiant', $etudiant->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
