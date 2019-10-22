<?php

namespace App\Services\FormHandler;

use App\Entity\Category;
use App\Entity\Form;
use App\Entity\FormArea;
use App\Entity\Widget;

interface FormHandlerInterface
{
    public function setFormAreaSize(FormArea $formArea, $size): void;
    public function sortForm(Form $form, array $mapPositionToAreaId): void;
    public function changeFormAreaWidgetType(Widget $formArea, ?string $newType): void;
    public function setWidgetSetting(Widget $widget, ?string $attribute, $value): void;
    public function setDraftForm(Category $category, bool $overwrite = false): void;
    public function publishDraftForm(Category $category): void;
}