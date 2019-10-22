<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AppController extends AbstractController
{
    /**
     * @Route(path="/", methods={"get"}, name="app.homepage")
     * @inheritdoc
     */
    function AppHomePage(): Response
    {
        return $this->render('homepage.html.twig');
    }
}