<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/Controller/administration/stage/StagePeriodeCourrierController.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

namespace App\Controller\administration\stage;

use App\Controller\BaseController;
use App\Entity\StageEtudiant;
use App\Entity\StagePeriode;
use App\Events;
use App\MesClasses\MyStageMailTemplate;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StagePeriodeCourrierController
 * @package App\Controller\administration\stage
 * @Route("/administration/stage/periode/courrier")
 */
class StagePeriodeCourrierController extends BaseController
{
    /**
     * @Route("/apercu/{mail}", name="administration_stage_periode_courrier_apercu_defaut")
     *
     * @param $mail
     *
     * @return Response
     */
    public function apercuDefaut($mail): Response
    {
        return $this->render('administration/stage/stage_periode_courrier/apercuDefaut.html.twig', [
            'mail' => Events::getConstante('MAIL_CHGT_' . $mail)
        ]);
    }

    /**
     * @Route("/sauvegarde-modele/{uuid}/{mail}", name="administration_stage_periode_courrier_sauvegarde_modele",
     *                                            options={"expose"=true})
     * @ParamConverter("stagePeriode", options={"mapping": {"uuid": "uuid"}})
     * @param MyStageMailTemplate $myStageMailTemplate
     * @param Request             $request
     * @param StagePeriode        $stagePeriode
     * @param                     $mail
     *
     * @return Response
     * @throws NonUniqueResultException
     */
    public function sauvegardeModele(
        MyStageMailTemplate $myStageMailTemplate,
        Request $request,
        StagePeriode $stagePeriode,
        $mail
    ): Response {
        $myStageMailTemplate->updateTemplate($mail, $request->request > get('sujet'), $request->request->get('message'),
            $stagePeriode);

        return $this->render('administration/stage/stage_periode_courrier/index.html.twig', [
            'etatsConvention' => StageEtudiant::ETATS,
            'stagePeriode'    => $stagePeriode,
        ]);
    }

    /**
     * @Route("/{uuid}", name="administration_stage_periode_courrier_index")
     * @ParamConverter("stagePeriode", options={"mapping": {"uuid": "uuid"}})
     * @param StagePeriode $stagePeriode
     *
     * @return Response
     */
    public function index(StagePeriode $stagePeriode): Response
    {

        $courriers = null;

        return $this->render('administration/stage/stage_periode_courrier/index.html.twig', [
            'etatsConvention' => StageEtudiant::ETATS,
            'stagePeriode'    => $stagePeriode,
            'courriers'       => $courriers
        ]);
    }


}
