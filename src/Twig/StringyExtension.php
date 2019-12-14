<?php

namespace App\Twig;

use Stringy\Stringy as S;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('slugify', [$this, 'slugify']),
        ];
    }

    public function slugify($value)
    {
        $stringy = S::create($value);
        $newValue = $stringy->slugify('-');

        return $newValue;
    }
}
