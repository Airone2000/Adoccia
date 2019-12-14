<?php

namespace App\Services\FicheHandler;

use App\Entity\Category;
use App\Entity\Fiche;

interface FicheHandlerInterface
{
    public function createFicheFromFicheTypeData(array $data, ?Fiche $fiche = null): Fiche;

    public function editFicheFromFicheTypeData(Fiche $fiche, array $data): Fiche;

    public function getFicheView(Fiche $fiche): string;

    public function mapValueToWidgetId(Fiche $fiche): array;

    public function unPublishInvalidFiches(Category $category): void;
}
