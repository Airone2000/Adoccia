<?php

namespace App\Services\FicheHandler;

use App\Entity\Fiche;

interface FicheHandlerInterface
{
    public function createFicheFromFicheTypeData(array $data): Fiche;
}