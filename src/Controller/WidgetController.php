<?php

namespace App\Controller;

use App\Entity\Widget;
use App\Services\FormHandler\FormHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/widgets")
 */
final class WidgetController extends AbstractController
{
    /**
     * @Route(
     *     path="/{id}/change-type",
     *     methods={"put"},
     *     name="widget.changeType",
     *     condition="request.isXmlHttpRequest() and request.headers.get('Content-Type') == 'application/json'"
     * )
     * @inheritdoc
     */
    function changeType(Widget $widget, Request $request, FormHandlerInterface $formHandler): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        try {
            $newType = json_decode($request->getContent(), true)['type'] ?? null;
            $formHandler->changeFormAreaWidgetType($widget, $newType);
            return new JsonResponse([
                'view' => $this->renderView('form/_area.html.twig', ['area' => $widget->getFormArea()])
            ]);
        }
        catch (\Exception $e) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route(
     *     path="/{id}/settings-view",
     *     methods={"get"},
     *     condition="request.isXmlHttpRequest()",
     *     name="widget.getSettingsView"
     * )
     * @inheritdoc
     */
    function getSettingsView(Widget $widget): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        try {
            $view = $this->renderView("form/builder/_settings_{$widget->getType()}.html.twig", [
                'form' => $widget->getFormArea()->getForm(),
                'area' => $widget->getFormArea(),
                'widget' => $widget
            ]);
            return new JsonResponse([
                'view' => $view
            ]);
        }
        catch (\Exception $e) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route(
     *     path="/{id}/set-setting",
     *     methods={"post"},
     *     condition="request.isXmlHttpRequest() and request.headers.get('Content-Type') == 'application/json'",
     *     name="widget.setSetting"
     * )
     * @inheritdoc
     */
    function setSetting(Widget $widget, Request $request, FormHandlerInterface $formHandler): Response
    {
        if (!$this->getUser()) {
            # Must be connected
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }

        try {
            $body = json_decode($request->getContent(), true) ?? [];
            $attribute = $body['attribute'] ?? null;
            $value = $body['value'] ?? null;

            if ($attribute === null) throw new \Exception('Attribute name cannot be null');

            $formHandler->setWidgetSetting($widget, $attribute, $value);
            return new Response('', Response::HTTP_NO_CONTENT);
        }
        catch (\Exception $e) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }
    }
}