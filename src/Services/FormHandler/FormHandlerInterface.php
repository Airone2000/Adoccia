<?php

namespace App\Services\FormHandler;

use App\Entity\Form;
use App\Entity\FormArea;

interface FormHandlerInterface
{
    public function setFormAreaSize(FormArea $formArea, $size): void;
    public function sortForm(Form $form, array $mapPositionToAreaId): void;
}