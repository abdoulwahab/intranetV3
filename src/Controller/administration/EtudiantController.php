<?php

namespace App\Controller\administration;

use App\Controller\BaseController;
use App\Entity\Constantes;
use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Form\ImportEtudiantType;
use App\Repository\EtudiantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EtudiantController
 * @package App\Controller\administration
 * @Route("/administration/etudiant")
 */
class EtudiantController extends BaseController
{
    /**
     * @Route("/", name="administration_etudiant_index")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(): Response
    {
        return $this->render('administration/etudiant/index.html.twig', [
        ]);
    }

//    /**
//     * @Route("/ajouter", name="administration_etudiant_new", methods="GET|POST")
//     */
//    public function create(): Response
//    {
//        return $this->render('administration/etudiant/new.html.twig');
//    }

    /**
     * @Route("/import", name="administration_etudiant_import", methods="POST")
     * @param Request $request
     *
     * @return Response
     */
    public function import(Request $request): Response
    {
        $form = $this->createForm(
            ImportEtudiantType::class,
            null,
            [
                'formation' => $this->dataUserSession->getFormation(),
                'attr'      => [
                    'data-provide' => 'validation'
                ]
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'etudiant.import.success.flash');
        }

        return $this->redirectToRoute('administration_etudiant_semestre_add');
    }

    /**
     * @Route("/add", name="administration_etudiant_add", methods="POST")
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $etudiant = new Etudiant();

        $form = $this->createForm(
            EtudiantType::class,
            $etudiant,
            [
                'attr'      => [
                    'data-provide' => 'validation'
                ]
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($etudiant);
            $this->entityManager->flush();

            $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'etudiant.add.success.flash');
        }

        return $this->redirectToRoute('administration_etudiant_semestre_add');
    }


    /**
     * @Route("/export.{_format}", name="administration_all_etudiant_export", methods="GET", requirements={"_format"="csv|xlsx|pdf"})
     */
    public function export(): Response
    {
        //save en csv
        return new Response('', Response::HTTP_OK);
    }
}
