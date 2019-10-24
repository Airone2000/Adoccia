<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Services\FicheHandler\FicheHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
}