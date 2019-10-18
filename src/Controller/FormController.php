<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\FormArea;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     path="/forms"
 * )
 */
final class FormController extends AbstractController
{
    /**
     * @Route(path="/{id}", methods={"get"}, name="form.show")
     * @inheritdoc
     */
    function show(Form $form): Response
    {
        return $this->render('form/show.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route(path="/{id}/edit", methods={"get", "post"}, name="form.edit")
     * @inheritdoc
     */
    function edit(Form $form): Response
    {
        return $this->render('form/edit.html.twig', [
            'form' => $form,
            '_blankArea' => new FormArea()
        ]);
    }
}