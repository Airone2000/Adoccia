<?php

namespace App\Controller;

use App\Entity\Widget;
use App\Form\WidgetSettingsType\AbstractWidgetSettingsType;
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
     *     path="/{id}/put-inmodal-settings",
     *     methods={"get", "put"},
     *     condition="request.isXmlHttpRequest()",
     *     name="widget.getSettingsView"
     * )
     * @IsGranted("GET_WIDGET_SETTING_VIEW", subject="widget")
     * @inheritdoc
     */
    function putInModalSettingsView(Widget $widget, Request $request): Response
    {
        try {

            $type = ucfirst(strtolower($widget->getType()));
            $typeClass = "\App\Form\WidgetSettingsType\\{$type}WidgetSettingsType";

            if (class_exists($typeClass)) {
                $form = $this->createForm($typeClass, $widget, [
                    'action' => $this->generateUrl('widget.getSettingsView', ['id' => $widget->getId()]),
                    'method' => 'put',
                    'validation_groups' => ["{$type}Widget:SetSettings"],
                    'mode' => AbstractWidgetSettingsType::MODE_COMPLETE
                ]);

                $form->handleRequest($request);
                if ($form->isSubmitted()) {
                    if ($form->isValid()) {
                        $this->getDoctrine()->getManager()->flush();

                        # Widget is definitely modified
                        # Returns the complete view so that client can update its display
                        return new JsonResponse([
                            'area' => $widget->getFormArea()->getId(),
                            'formAreaView' => $this->renderView('form/_area.html.twig', ['area' => $widget->getFormArea()])
                        ], Response::HTTP_OK);
                    }
                    else $status = Response::HTTP_BAD_REQUEST;
                }
                else $status = Response::HTTP_OK;

                $view = $this->renderView("form/builder/_settings_{$widget->getType()}.html.twig", [
                    'form' => $form->createView()
                ]);
                return new JsonResponse([
                    'view' => $view
                ], $status);
            }
            else throw new \LogicException("Class {$typeClass} must exist.");
        }
        catch (\Exception $e) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
    }
}