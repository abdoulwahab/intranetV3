<?php

namespace App\Controller\administration;

use App\Controller\BaseController;
use App\Entity\Constantes;
use App\Entity\Semestre;
use App\Entity\TypeGroupe;
use App\Form\TypeGroupeType;
use App\Repository\TypeGroupeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/administration/type-groupe")
 */
class TypeGroupeController extends BaseController
{
    /**
     * @Route("/new/{semestre}", name="administration_type_groupe_new", methods="GET|POST")
     */
    public function create(Request $request, Semestre $semestre): Response
    {
        $typeGroupe = new TypeGroupe($semestre);
        $form = $this->createForm(TypeGroupeType::class, $typeGroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($typeGroupe);
            $this->entityManager->flush();
            $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'type_groupe.add.success.flash');

            return $this->redirectToRoute('administration_groupe_index');
        }

        return $this->render('administration/type_groupe/new.html.twig', [
            'type_groupe' => $typeGroupe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="administration_type_groupe_show", methods="GET")
     */
    public function show(TypeGroupe $typeGroupe): Response
    {
        return $this->render('administration/type_groupe/show.html.twig', ['type_groupe' => $typeGroupe]);
    }

    /**
     * @Route("/{id}/edit", name="administration_type_groupe_edit", methods="GET|POST")
     */
    public function edit(Request $request, TypeGroupe $typeGroupe): Response
    {
        $form = $this->createForm(TypeGroupeType::class, $typeGroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'type_groupe.edit.success.flash');

            return $this->redirectToRoute('administration_type_groupe_edit', ['id' => $typeGroupe->getId()]);
        }

        return $this->render('administration/type_groupe/edit.html.twig', [
            'type_groupe' => $typeGroupe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/duplicate", name="administration_type_groupe_duplicate", methods="GET|POST")
     * @param TypeGroupe                 $typeGroupe
     *
     * @return Response
     */
    public function duplicate(TypeGroupe $typeGroupe): Response
    {
        $newGroupe = clone $typeGroupe;

        $this->entityManager->persist($newGroupe);
        $this->entityManager->flush();
        $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'type_groupe.duplicate.success.flash');

        return $this->redirectToRoute('administration_groupe_edit', ['id' => $newGroupe->getId()]);
    }

    /**
     * @Route("/{id}", name="administration_type_groupe_delete", methods="DELETE")
     * @param Request $request
     * @param TypeGroupe  $typeGroupe
     *
     * @return Response
     */
    public function delete(Request $request, TypeGroupe $typeGroupe): Response
    {//todo: tester delete cascade et la suppression ds groupes et des étudiants affectés.

        $id = $typeGroupe->getId();
        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $this->entityManager->remove($typeGroupe);
            $this->entityManager->flush();
            $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'type_groupe.delete.success.flash');

            return $this->json($id, Response::HTTP_OK);
        }

        $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'type_groupe.delete.error.flash');

        return $this->json(false, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
