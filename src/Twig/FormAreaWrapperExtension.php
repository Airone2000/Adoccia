<?php

namespace App\Twig;

use App\Entity\FormArea;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormAreaWrapperExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('form_area_start', [$this, 'formAreaStart'], ['is_safe' => ['html']]),
            new TwigFunction('form_area_end', [$this, 'formAreaEnd'], ['is_safe' => ['html']]),
        ];
    }

    public function formAreaStart(FormArea $value)
    {
        return $this->twig->render('extensions/formAreaWrapper/start.html.twig', ['area' => $value]);
    }

    public function formAreaEnd(FormArea $value)
    {
        return $this->twig->render('extensions/formAreaWrapper/end.html.twig', ['area' => $value]);
    }
}
