<?php

namespace App\Validator;

use App\Entity\Picture;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PictureIsSquareValidator extends ConstraintValidator
{
    /**
     * @var string
     */
    private $pictureUploadDir;

    public function __construct(string $pictureUploadDir)
    {
        $this->pictureUploadDir = $pictureUploadDir;
    }

    /**
     * @param Constraint|PictureIsSquare $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\PictureIsSquare */

        if ($value instanceof Picture) {
            $pathToFile = $this->pictureUploadDir.\DIRECTORY_SEPARATOR.$value->getFilename();
            if (file_exists($pathToFile)) {
                [$width, $height] = getimagesize($pathToFile);
                if ($width !== $height) {
                    $this->context
                        ->buildViolation('Must be a square')
                        ->addViolation()
                    ;
                }
            }
        }
    }
}
