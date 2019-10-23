<?php

namespace App\Controller;

use App\Entity\Widget;
use App\Services\FormHandler\FormHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/widgets")
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
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
     * @IsGranted("CHANGE_WIDGET_TYPE", subject="widget")
     * @inheritdoc
     */
    function changeType(Widget $widget, Request $request, FormHandlerInterface $formHandler): Response
    {
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
     * @IsGranted("GET_WIDGET_SETTING_VIEW", subject="widget")
     * @inheritdoc
     */
    function getSettingsView(Widget $widget): Response
    {
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
     * @IsGranted("SET_WIDGET_SETTING", subject="widget")
     * @inheritdoc
     */
    function setSetting(Widget $widget, Request $request, FormHandlerInterface $formHandler): Response
    {
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