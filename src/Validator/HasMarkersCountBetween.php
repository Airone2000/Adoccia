<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HasMarkersCountBetween extends Constraint
{
    public $minMessage = 'This map should contains at least {{ value }} markers.';
    public $maxMessage = 'This map should contains no more than {{ value }} markers.';

    /**
     * @var int|null
     * The number of min markers required
     */
    public $min;

    /**
     * @var int|null
     * The number of min markers required
     */
    public $max;
}
