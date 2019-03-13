<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2019-03-12
 * Time: 15:11
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * Class FacturaEstaTimbrada
 * @package AppBundle\Validator\Constraints
 */
class FacturaEstaTimbrada extends Constraint
{
    public $message = 'No se pudo sellar la factura, Error: {{ error }}';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
