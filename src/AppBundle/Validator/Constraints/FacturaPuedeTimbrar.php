<?php
/**
 * User: inrumi
 * Date: 9/3/18
 * Time: 16:54
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

class FacturaPuedeTimbrar extends Constraint
{
    public $message = 'La factura no pudo ser timbrada, razón: {{ mensaje }}';
}
