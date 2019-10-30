<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Fiche;
use App\Enum\FicheModeEnum;
use App\Form\FicheType;
use App\Services\FicheHandler\FicheHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FicheController extends AbstractController
{
    /**
     * @Route("/categories/{categoryId}/fiches/{ficheId}", methods={"get"}, name="fiche.show")
     * @Entity(name="category", expr="repository.find(categoryId)")
     * @Entity(name="fiche", expr="repository.findFicheByCategoryAndId(categoryId, ficheId)")
     * @IsGranted("CAN_SEE_FICHE", subject="fiche")
     * @inheritdoc
     */
    function showFiche(Category $category, Fiche $fiche, FicheHandlerInterface $ficheHandler): Response
    {
        return $this->render('fiche/show.html.twig', [
            'category' => $category,
            'fiche' => $fiche,
            'ficheView' => $ficheHandler->getFicheView($fiche)
        ]);
    }

    /**
     * @Route("/categories/{categoryId}/fiches/{ficheId}/edit", methods={"get", "put"}, name="fiche.edit")
     * @Entity(name="category", expr="repository.find(categoryId)")
     * @Entity(name="fiche", expr="repository.findFicheByCategoryAndId(categoryId, ficheId)")
     * @inheritdoc
     */
    function editFiche(Category $category, Fiche $fiche, Request $request, FicheHandlerInterface $ficheHandler): Response
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
            'category' => $category,
            'fiche' => $fiche,
            'form' => $form->createView()
        ]);
    }
}