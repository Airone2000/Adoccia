<?php

namespace App\Controller;

use App\Entity\FormArea;
use App\Services\FormHandler\FormHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/form-areas")
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
final class FormAreaController extends AbstractController
{
    /**
     * @Route("/{id}", methods={"delete"}, condition="request.isXmlHttpRequest()", name="formArea.delete")
     * @IsGranted("DELETE_FORM_AREA", subject="formArea")
     * @inheritdoc
     */
    function deleteFormArea(FormArea $formArea): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($formArea);
        $em->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     path="/{id}/set-width",
     *     methods={"put"},
     *     condition="request.isXmlHttpRequest() and request.headers.get('Content-Type') == 'application/json'",
     *     name="formArea.setWidth"
     * )
     * @IsGranted("SET_FORM_AREA_WIDTH", subject="formArea")
     * @inheritdoc
     */
    function setWidth(FormArea $formArea, Request $request, FormHandlerInterface $formHandler): Response
    {
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
}