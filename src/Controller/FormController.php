<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\FormArea;
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
     *     condition="request.isXmlHttpRequest()",
     *     name="form.addArea"
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
     *     path="/{id}/form-areas/sort",
     *     methods={"post"},
     *     condition="request.isXmlHttpRequest() and request.headers.get('Content-Type') == 'application/json'",
     *     name="form.sortAreas"
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
}