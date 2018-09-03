<?php
/**
 * User: inrumi
 * Date: 9/3/18
 * Time: 16:56
 */

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Contabilidad\Facturacion;
use Hyperion\MultifacturasBundle\src\Multifacturas;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FacturaPuedeTimbrarValidator extends ConstraintValidator
{
    /**
     * @var Multifacturas
     */
    private $facturador;

    public function __construct(Multifacturas $facturador)
    {
        $this->facturador = $facturador;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param Facturacion $factura
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($factura, Constraint $constraint)
    {
        $timbrado = $this->facturador->procesa($factura);

        // Verificar que la factura se haya timbrado correctamente
        if (isset($timbrado['codigo_mf_texto']) || isset($timbrado['codigo_mf_numero'])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ codigo }}', $timbrado['codigo_mf_numero'])
                ->setParameter('{{ mensaje }}', $timbrado['codigo_mf_texto'])
                ->addViolation();
        }
    }

}
