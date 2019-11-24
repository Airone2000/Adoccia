<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureUploaderType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PictureController extends AbstractController
{
    private $pictureUploadDir;
    private $picturePublicUploadDir;

    public function __construct($pictureUploadDir, $picturePublicUploadDir)
    {
        $this->pictureUploadDir = $pictureUploadDir;
        $this->picturePublicUploadDir = $picturePublicUploadDir;
    }

    /**
     * @Route(
     *     path="/pictures/upload-picture",
     *     methods={"get", "post"},
     *     condition="request.isXmlHttpRequest()",
     *     name="picture.uploadPicture"
     * )
     * @inheritdoc
     */
    public function uploadPicture(Request $request)
    {
        $form = $this->createForm(PictureUploaderType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $base64Picture = $data['base64Picture'];

            if (preg_match('/^data:image\/(?<extension>(?:png|gif|jpg|jpeg));base64,(?<image>.+)$/', $base64Picture, $matchings)) {
                $imageData = base64_decode($matchings['image']);
                $extension = $matchings['extension'];
                $filename = uniqid('picture_') . '.' . $extension;

                if (file_put_contents($this->pictureUploadDir . DIRECTORY_SEPARATOR . $filename, $imageData)) {
                    $picture = new Picture();
                    $picture
                        ->setUser($this->getUser())
                        ->setFilename($filename)
                    ;

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($picture);
                    $em->flush();

                    return new JsonResponse([
                        'pictureId' => $picture->getId(),
                        'pictureURL' => $request->getSchemeAndHttpHost() . DIRECTORY_SEPARATOR . $this->picturePublicUploadDir . DIRECTORY_SEPARATOR . $filename
                    ]);
                }
            }

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'view' => $this->renderView('_picture_uploader.html.twig', ['form' => $form->createView()])
        ]);
    }
}