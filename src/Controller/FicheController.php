<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Entity\Value;
use App\Enum\FicheModeEnum;
use App\Form\FicheType;
use App\Services\FicheHandler\FicheHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FicheController extends AbstractController
{
    /**
     * @Route("/fiches/{id}", methods={"get"}, name="fiche.show")
     * @IsGranted("CAN_SEE_FICHE", subject="fiche")
     * @inheritdoc
     */
    function showFiche(Fiche $fiche, FicheHandlerInterface $ficheHandler): Response
    {
        return $this->render('fiche/show.html.twig', [
            'fiche' => $fiche,
            'ficheView' => $ficheHandler->getFicheView($fiche)
        ]);
    }

    /**
     * @Route("/fiches/{id}/edit", methods={"get", "put"}, name="fiche.edit")
     * @inheritdoc
     */
    function editFiche(Fiche $fiche, Request $request, FicheHandlerInterface $ficheHandler): Response
    {

        $data = ['title' => $fiche->getTitle(), 'published' => $fiche->isPublished()];
        $data = $data + $ficheHandler->mapValueToWidgetId($fiche);

        $form = $this->createForm(FicheType::class, $data, [
            'category' => $fiche->getCategory(),
            'mode' => FicheModeEnum::EDITION,
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $ficheHandler->editFicheFromFicheTypeData($fiche, $form->getData());
                $this->addFlash('editFicheSuccess', '');
                return $this->redirectToRoute('fiche.show', ['id' => $fiche->getId()]);
            }
            catch (\Exception $e) {
                $this->addFlash('editFicheError', '');
                return $this->redirectToRoute('fiche.edit', ['id' => $fiche->getId()]);
            }
        }

        return $this->render('fiche/edit.html.twig', [
            'fiche' => $fiche,
            'form' => $form->createView()
        ]);
    }
}