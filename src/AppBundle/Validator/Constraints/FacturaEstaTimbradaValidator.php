<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2019-03-12
 * Time: 15:13
 */

namespace AppBundle\Validator\Constraints;


use AppBundle\Entity\Contabilidad\Facturacion;
use Hyperion\MultifacturasBundle\src\Multifacturas;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FacturaEstaTimbradaValidator extends ConstraintValidator
{
    /**
     * @var Multifacturas
     */
    private $multifacturas;

    public function __construct(Multifacturas $multifacturas)
    {
        $this->multifacturas = $multifacturas;
    }

    /**
     * Constraint para timbrar las facturas, si el timbrado pasa correctamente, entonces la validación
     * del formulario continua
     *
     * @param Facturacion $factura
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($factura, Constraint $constraint)
    {
        if (!$constraint instanceof FacturaEstaTimbrada) {
            throw new UnexpectedTypeException($constraint, FacturaEstaTimbrada::class);
        }

        if (null === $factura || '' === $factura) {
            return;
        }

        if ($factura->isPreview) {
            return;
        }

        $sello = $this->multifacturas->procesa($factura);

        if (key_exists('codigo_mf_numero', $sello)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ error }}', $sello['codigo_mf_texto'])
                ->addViolation();
        }
    }
}
