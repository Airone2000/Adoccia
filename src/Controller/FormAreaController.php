<?php

namespace App\Controller;

use App\Entity\FormArea;
use App\Services\FormHandler\FormHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/form-areas")
 */
final class FormAreaController extends AbstractController
{
    /**
     * @Route("/{id}", methods={"delete"}, condition="request.isXmlHttpRequest()", name="formArea.delete")
     * @inheritdoc
     */
    function deleteFormArea(FormArea $formArea): Response
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
     *     path="/{id}/set-width",
     *     methods={"put"},
     *     condition="request.isXmlHttpRequest() and request.headers.get('Content-Type') == 'application/json'",
     *     name="formArea.setWidth"
     * )
     * @inheritdoc
     */
    function setWidth(FormArea $formArea, Request $request, FormHandlerInterface $formHandler): Response
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
}