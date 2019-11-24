<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Enum\FicheModeEnum;
use App\Form\FicheType;
use App\Security\Voter\FicheVoter;
use App\Services\FicheHandler\FicheHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     path="/fiches"
 * )
 */
final class FicheController extends AbstractController
{
    /**
     * @Route("/{id}", methods={"get"}, name="fiche.show")
     * @Entity(name="fiche", expr="repository.getOneForUserById(null, id)")
     * @inheritdoc
     */
    function showFiche(Fiche $fiche, FicheHandlerInterface $ficheHandler): Response
    {
        if (FicheVoter::canSeeFiche($this->getUser(), $fiche)) {
            return $this->render('fiche/show.html.twig', [
                'category' => $fiche->getCategory(),
                'fiche' => $fiche,
                'ficheView' => $ficheHandler->getFicheView($fiche)
            ]);
        }
        return $this->redirectToRoute('category.show', [
            'id' => $fiche->getCategory()->getId()
        ]);
    }

    /**
     * @Route("/{id}/edit", methods={"get", "put"}, name="fiche.edit")
     * @Entity(name="fiche", expr="repository.getOneForUserById(null, id)")
     * @IsGranted("CAN_EDIT_FICHE", subject="fiche")
     * @inheritdoc
     */
    function editFiche(Fiche $fiche, Request $request, FicheHandlerInterface $ficheHandler): Response
    {
        $data = [
            'title' => $fiche->getTitle(),
            'published' => $fiche->isPublished(),
            'picture' => $fiche->getPicture()
        ];
        $data = $data + $ficheHandler->mapValueToWidgetId($fiche);

        $form = $this->createForm(FicheType::class, $data, [
            'category' => $fiche->getCategory(),
            'mode' => FicheModeEnum::EDITION,
            'method' => 'PUT',
            'fiche' => $fiche
        ]);

        $category = $fiche->getCategory();
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