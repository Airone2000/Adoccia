<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
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