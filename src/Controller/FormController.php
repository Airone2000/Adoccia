<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\FormArea;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    function setFormAreaSize(Form $form, FormArea $formArea, Request $request, ParameterBagInterface $parameterBag): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        $body = json_decode($request->getContent(), true);
        $areaMinSize = +$parameterBag->get('areaMinSize');
        $areaMaxSize = +$parameterBag->get('areaMaxSize');

        if ($size = ($body['size'] ?? null)) {
            $size = +$size;
            if (is_numeric($size)) {
                if ($size >= $areaMinSize && $size <= $areaMaxSize) {
                    $formArea->setWidth($size);
                    $this->getDoctrine()->getManager()->flush();
                    return new Response('', Response::HTTP_NO_CONTENT);
                }
            }
        }

        return new Response('', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route(
     *     path="/{id}/form-areas/sort",
     *     methods={"post"},
     *     condition="request.isXmlHttpRequest() and request.headers.get('Content-Type') == 'application/json'"
     * )
     * @inheritdoc
     */
    function sortFormArea(Form $form, Request $request): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        $mapPositionToAreaId = json_decode($request->getContent(), true);

        $sizesToConsume = range(1, $form->getAreas()->count());
        $sizesToConsume = array_flip($sizesToConsume);

        /** @var FormArea $area */
        foreach ($form->getAreas() as $area) {
            # Position is given for this area is this position is not already used
            if (isset($mapPositionToAreaId[$area->getId()]) && isset($sizesToConsume[$mapPositionToAreaId[$area->getId()]])) {
                $position = $mapPositionToAreaId[$area->getId()];
                $area->setPosition($position);
                unset($sizesToConsume[$position]);
            }
            else {
                return new Response('', Response::HTTP_BAD_REQUEST);
            }
        }

        $this->getDoctrine()->getManager()->flush();
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}