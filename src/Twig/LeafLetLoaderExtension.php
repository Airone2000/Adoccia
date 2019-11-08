<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LeafLetLoaderExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('load_Leaflet_script', [$this, 'loadLeafLetScript']),
            new TwigFunction('load_Leaflet_style', [$this, 'loadLeafLetStyle']),
        ];
    }

    public function loadLeafLetScript()
    {
        echo '<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>';
    }

    public function loadLeafLetStyle()
    {
        echo '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>';
    }
}
