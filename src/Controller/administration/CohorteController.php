<?php

namespace App\Controller\administration;

use App\Controller\BaseController;
use App\Entity\Scolarite;
use App\Repository\EtudiantRepository;
use App\Repository\ParcourRepository;
use App\Repository\ScolariteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CohorteController
 * @package App\Controller\administration
 * @Route("/administration/cohorte")
 */
class CohorteController extends BaseController
{
    /**
     * @Route("/{annee}", name="administration_cohorte_index")
     */
    public function index(EtudiantRepository $etudiantRepository, ScolariteRepository $scolariteRepository, $annee = 0): Response
    {
        if ($annee === 0) {
            $annee = date('Y')-1;
        }
        //on ne récupère la cohorte que de la formation.
        //$etudiants = $etudiantRepository->findEtudiantCohorte($annee, $this->dataUserSession->getFormation());
        $parcours = $scolariteRepository->findEtudiantsFormation($annee, $this->dataUserSession->getFormation());
           $etudiants = array();

           /** @var Scolarite $parcour */
        foreach ($parcours as $parcour)
        {
            if ($parcour->getEtudiant() !== null && ! array_key_exists($parcour->getEtudiant()->getId(), $etudiants)) {
                $etudiants[$parcour->getEtudiant()->getId()] = $parcour->getEtudiant();
            }
        }
        return $this->render('administration/cohorte/index.html.twig', [
            'parcours'  => $parcours,
            'etudiants' => $etudiants,
            'annee' => $annee
        ]);
    }

    /**
     * @Route("/export.{_format}", name="administration_cohorte_export", requirements={"_format"="csv|xlsx|pdf"})
     */
    public function export(): void
    {
    }
}
