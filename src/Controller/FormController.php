<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Entity\Form;
use App\Entity\FormArea;
use App\Enum\FicheModeEnum;
use App\Form\FicheType;
use App\Repository\CategoryRepository;
use App\Services\FormHandler\FormHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/forms")
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
final class FormController extends AbstractController
{
    /**
     * @Route(path="/{id}/edit", methods={"get", "post"}, name="draftForm.edit")
     * @IsGranted("EDIT_DRAFT_FORM", subject="form")
     * @inheritdoc
     */
    function editDraftForm(Form $form, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['draftForm' => $form]);
        if ($category instanceof Category) {
            return $this->render('form/edit.html.twig', [
                'form' => $form,
                'category' => $category
            ]);
        }

        # Maybe redirect to an error page
        return $this->redirectToRoute('category.index');
    }

    /**
     * @Route(
     *     path="/{id}/form-areas",
     *     methods={"post"},
     *     condition="request.isXmlHttpRequest()",
     *     name="draftForm.addArea"
     * )
     * @IsGranted("ADD_FORM_AREA_TO_DRAFT_FORM", subject="form")
     * @inheritdoc
     */
    function addFormAreaToDraftForm(Form $form, FormHandlerInterface $formHandler): Response
    {
        $formArea = $formHandler->addFormAreaToDraftForm($form);
        $body = [
            'view' => $this->renderView('form/_area.html.twig', ['area' => $formArea])
        ];
        return new JsonResponse($body);
    }

    /**
     * @Route(
     *     path="/{id}/form-areas/sort",
     *     methods={"post"},
     *     condition="request.isXmlHttpRequest() and request.headers.get('Content-Type') == 'application/json'",
     *     name="draftForm.sortAreas"
     * )
     * @IsGranted("SORT_DRAFT_FORM_AREAS", subject="form")
     * @inheritdoc
     */
    function sortDraftFormAreas(Form $form, Request $request, FormHandlerInterface $formHandler): Response
    {
        try {
            $mapPositionToAreaId = json_decode($request->getContent(), true);
            $formHandler->sortForm($form, $mapPositionToAreaId);
            return new Response('', Response::HTTP_NO_CONTENT);
        }
        catch (\Exception $e) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route(path="/{id}/publish", methods={"post"}, name="draftForm.publish")
     * @IsGranted("PUBLISH_DRAFT_FORM", subject="form")
     * @inheritdoc
     */
    function publishDraftForm(Form $form, CategoryRepository $categoryRepository, FormHandlerInterface $formHandler): Response
    {
        try {
            $category = $categoryRepository->findOneBy(['draftForm' => $form]);
            if ($category === null) throw new \Exception('Not a draft form or deleted category.');
            $formHandler->publishDraftForm($category);
            $this->addFlash('draftFormPublished', '');
            return new Response('', Response::HTTP_NO_CONTENT);
        }
        catch (\Exception $e) {
            return new Response('', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @Route(path="/{id}/delete-draft", methods={"delete"}, name="draftForm.delete")
     * @IsGranted("DELETE_DRAFT_FORM", subject="form")
     * @inheritdoc
     */
    function deleteDraftForm(Form $form): Response
    {
        # Thanks to database architecture, draft_form is automatically set to null on Category
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($form);
            $em->flush();
            $this->addFlash('draftFormDeleted', '');
            return new Response('', Response::HTTP_NO_CONTENT);
        }
        catch (\Exception $e) {
            return new Response('Unable to remove this draft form. Retry later.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @Route("/{id}/preview", methods={"get"}, name="draftForm.preview", condition="request.isXmlHttpRequest()")
     * @IsGranted("PREVIEW_DRAFT_FORM", subject="form")
     * @inheritdoc
     */
    function preview(Form $form, CategoryRepository $categoryRepository): Response
    {
        $fiche =new Fiche();
        $fiche->setCreator($this->getUser());

        $category = $categoryRepository->findOneBy(['draftForm' => $form]);
        $form = $this->createForm(FicheType::class, null, [
            'category' => $category,
            'mode' => FicheModeEnum::EDITION,
            'disabled' => true,
            'is_form_preview' => true,
            'fiche' => $fiche
        ]);

        $view = $this->renderView('form/_preview.html.twig', [
            'form' => $form->createView(),
            'fiche' => $fiche
        ]);

        return new JsonResponse([
            'view' => $view
        ]);
    }
}