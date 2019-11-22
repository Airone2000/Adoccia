<?php

namespace App\Validator;

use App\Entity\Picture;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CategoryPictureValidator extends ConstraintValidator
{
    /**
     * @param Picture $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\CategoryPicture */

        if (null === $value || '' === $value) {
            return;
        }

        if ($value->getUploadedFile() instanceof UploadedFile) {
            # Make sure :
            # Attribute cropCoords is an array
            if (($cropCoords = $value->getCropCoords()) === null) {
                $this->context->buildViolation('cropCoords should be an array')->addViolation();
                return;
            }

            # cropCoords has right shape
            if (
                isset($cropCoords['x']) && is_numeric($cropCoords['x']) &&
                isset($cropCoords['y']) && is_numeric($cropCoords['y']) &&
                isset($cropCoords['width']) && is_numeric($cropCoords['width']) &&
                isset($cropCoords['height']) && is_numeric($cropCoords['height'])
            ) {
                # The selected area is a square
                $selectedSizes = [+$cropCoords['width'], +$cropCoords['height']];
                if ($selectedSizes[0] !== $selectedSizes[1]) {
                    $this->context->buildViolation('Selection should be a square')->addViolation();
                    return;
                }

                # The selected area fits in the original image
                $originalImageSizes = getimagesize($value->getUploadedFile());
                $totalSelectedWidth = +$cropCoords['x'] + $selectedSizes[0];
                $totalSelectedHeight = +$cropCoords['y'] + $selectedSizes[1];
                if ($totalSelectedWidth > $originalImageSizes[0] || $totalSelectedHeight > $originalImageSizes[1]) {
                    $this->context->buildViolation('Selection is out of the picture')->addViolation();
                    return;
                }
            }
            else {
                $this->context->buildViolation('cropCoords has missing keys/values')->addViolation();
                return;
            }

        }
    }
}
