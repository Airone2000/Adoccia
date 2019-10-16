<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HasPermissionExtension extends AbstractExtension
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
            new TwigFilter('hasPermission', [$this, 'checkIfUserHasPermission']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('hasPermission', [$this, 'checkIfUserHasPermission']),
        ];
    }

    public function checkIfUserHasPermission($value)
    {
        if ($token = $this->tokenStorage->getToken()) {
            /** @var User $user */
            $user = $token->getUser();
            return $user->hasPermission($value);
        }

        return false;
    }
}
