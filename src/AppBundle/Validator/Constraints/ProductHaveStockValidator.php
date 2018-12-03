<?php
/**
 * User: inrumi
 * Date: 7/24/18
 * Time: 13:44
 */

namespace AppBundle\Validator\Constraints;


use AppBundle\Entity\Tienda\Venta\Concepto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ProductHaveStockValidator
 * @package AppBundle\Validator\Constraints
 */
class ProductHaveStockValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Checa si hay stock en el momento de su submit
     *
     * @param Concepto $concepto El concepto que se validara
     * @param Constraint $constraint La constraint para la validacion
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validate($concepto, Constraint $constraint)
    {
        if (null === $concepto || null === $concepto->getProducto()) {
            return;
        }

        $requiredQuantity = $concepto->getCantidad();

        $stockQuantity = $concepto->getProducto()->getExistencia();

        if ($requiredQuantity > $stockQuantity) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ quantity }}', $stockQuantity ?? 0)
                ->atPath('cantidad')
                ->addViolation();
        }

    }
}
