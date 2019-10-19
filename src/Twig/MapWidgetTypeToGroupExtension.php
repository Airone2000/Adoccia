<?php

namespace App\Twig;

use App\Entity\User;
use App\Enum\WidgetTypeEnum;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MapWidgetTypeToGroupExtension extends AbstractExtension
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('mapWidgetTypeToGroup', [$this, 'returnMapWidgetTypeToGroup']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('mapWidgetTypeToGroup', [$this, 'returnMapWidgetTypeToGroup']),
        ];
    }

    public function returnMapWidgetTypeToGroup()
    {
        return WidgetTypeEnum::getGroupedWidgetTypes();
    }
}
