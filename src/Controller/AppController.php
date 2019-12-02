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

    /**
     * @Route(path="/a-propos", methods={"get"}, name="app.about", defaults={"menu": "about"})
     * @inheritdoc
     */
    function about(): Response
    {
        return $this->render('about.html.twig');
    }

    /**
     * @Route(path="/test")
     * @inheritdoc
     */
    function test(): Response
    {
        return $this->render('test.html.twig');
    }
}