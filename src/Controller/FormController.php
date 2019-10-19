<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\FormArea;
use App\Enum\WidgetTypeEnum;
use App\Services\FormHandler\FormHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     path="/forms"
 * )
 */
final class FormController extends AbstractController
{
    /**
     * @Route(path="/{id}", methods={"get"}, name="form.show")
     * @inheritdoc
     */
    function show(Form $form): Response
    {
        return $this->render('form/show.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route(path="/{id}/edit", methods={"get", "post"}, name="form.edit")
     * @inheritdoc
     */
    function edit(Form $form): Response
    {
        return $this->render('form/edit.html.twig', [
            'form' => $form,
            '_blankArea' => new FormArea()
        ]);
    }

    /**
     * @Route(
     *     path="/{id}/form-areas",
     *     methods={"post"},
     *     condition="request.isXmlHttpRequest()"
     * )
     * @inheritdoc
     */
    function addFormArea(Form $form): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        $form->addArea($formArea = new FormArea());
        $this->getDoctrine()->getManager()->flush();

        $body = [
            'view' => $this->renderView('form/_area.html.twig', ['area' => $formArea])
        ];
        return new JsonResponse($body);
    }

    /**
     * @Route(
     *     path="/{formId}/form-areas/{formAreaId}",
     *     methods={"delete"},
     *     condition="request.isXmlHttpRequest()"
     * )
     * @Entity(name="form", expr="repository.find(formId)")
     * @Entity(name="formArea", expr="repository.find(formAreaId)")
     * @inheritdoc
     */
    function deleteFormArea(Form $form, FormArea $formArea): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($formArea);
        $em->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     path="/{formId}/form-areas/{formAreaId}/set-size",
     *     methods={"put"},
     *     condition="request.isXmlHttpRequest() and request.headers.get('Content-Type') == 'application/json'"
     * )
     * @Entity(name="form", expr="repository.find(formId)")
     * @Entity(name="formArea", expr="repository.find(formAreaId)")
     * @inheritdoc
     */
    function setFormAreaSize(Form $form, FormArea $formArea, Request $request, FormHandlerInterface $formHandler): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        try {
            $body = json_decode($request->getContent(), true);
            $size = +($body['size'] ?? null);
            $formHandler->setFormAreaSize($formArea, $size);
            return new Response('', Response::HTTP_NO_CONTENT);
        }
        catch (\Exception $e) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route(
     *     path="/{id}/form-areas/sort",
     *     methods={"post"},
     *     condition="request.isXmlHttpRequest() and request.headers.get('Content-Type') == 'application/json'"
     * )
     * @inheritdoc
     */
    function sortFormArea(Form $form, Request $request, FormHandlerInterface $formHandler): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

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
     * @Route(
     *     path="/{formId}/form-areas/{formAreaId}/change-type",
     *     methods={"put"},
     *     condition="request.isXmlHttpRequest() and request.headers.get('Content-Type') == 'application/json'"
     * )
     * @Entity(name="form", expr="repository.find(formId)")
     * @Entity(name="formArea", expr="repository.find(formAreaId)")
     * @inheritdoc
     */
    function changeFormAreaWidgetType(Form $form, FormArea $formArea, Request $request, FormHandlerInterface $formHandler): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        try {
            $newType = json_decode($request->getContent(), true)['type'] ?? null;
            $formHandler->changeFormAreaWidgetType($formArea, $newType);
            return new JsonResponse([
                'view' => $this->renderView('form/_area.html.twig', ['area' => $formArea])
            ]);
        }
        catch (\Exception $e) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route(
     *     path="/{formId}/form-areas/{formAreaId}/get-settings-view",
     *     methods={"get"},
     *     condition="request.isXmlHttpRequest()"
     * )
     * @Entity(name="form", expr="repository.find(formId)")
     * @Entity(name="formArea", expr="repository.find(formAreaId)")
     * @inheritdoc
     */
    function getFormAreaWidgetSettingsView(Form $form, FormArea $formArea): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        try {
            $view = $this->renderView("form/builder/_settings_{$formArea->getWidget()->getType()}.html.twig", [
                'area' => $formArea
            ]);
            return new JsonResponse([
                'view' => $view
            ]);
        }
        catch (\Exception $e) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
    }
}