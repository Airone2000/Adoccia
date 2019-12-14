<?php

namespace App\Twig;

use App\Entity\Picture;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PictureDisplayerExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var string
     */
    private $picturePublicUploadDir;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(Environment $twig, string $picturePublicUploadDir, RequestStack $requestStack)
    {
        $this->twig = $twig;
        $this->picturePublicUploadDir = $picturePublicUploadDir;
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('showPicture', [$this, 'showPicture'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function showPicture($value, $filter = null)
    {
        if ($value instanceof Picture) {
            $url = $this->requestStack->getMasterRequest()->getSchemeAndHttpHost();
            $url .= \DIRECTORY_SEPARATOR;
            $url .= $this->picturePublicUploadDir;
            $url .= \DIRECTORY_SEPARATOR;
            $url .= $value->getFilename();

            return $this->twig->render('_picture.html.twig', [
                'url' => $url,
                'filter' => $filter,
            ]);
        }
    }
}
