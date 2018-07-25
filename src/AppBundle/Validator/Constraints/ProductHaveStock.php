<?php
/**
 * User: inrumi
 * Date: 7/24/18
 * Time: 13:38
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * Class ProductHaveStock
 * @package AppBundle\Validator\Constraints
 */
class ProductHaveStock extends Constraint
{
    public $message = 'Solo hay en existencia {{ quantity }} productos en el inventario';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
