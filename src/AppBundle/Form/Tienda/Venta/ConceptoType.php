<?php

namespace AppBundle\Form\Tienda\Venta;

use AppBundle\Form\EventListener\ProductoFieldListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ConceptoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Security
     */
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $moneySetting = [
            'currency' => 'MXN',
            'divisor' => 100,
            'grouping' => true,
            'data' => 0,
            'attr' => ['class' => 'money-input'],
        ];

        $discountAttributes = ['class' => 'discount-input'];

        if (!$this->security->isGranted('ROLE_ADMIN_POV')) {
            $discountAttributes['disabled'] = 'disabled';
        }

        $builder->addEventSubscriber(new ProductoFieldListener($this->entityManager));

        $builder->add(
            'cantidad',
            IntegerType::class,
            [
                'data' => 0,
                'attr' => ['min' => 0],
            ]
        );

        $builder->add(
            'precioUnitario',
            MoneyType::class,
            $moneySetting
        );

        $builder->add(
            'descuento',
            PercentType::class,
            [
                'data' => 0,
                'type' => 'integer',
                'required' => false,
                'attr' => $discountAttributes,
            ]
        );

        $builder->add(
            'iva',
            MoneyType::class,
            $moneySetting
        );

        $builder->add(
            'subtotal',
            MoneyType::class,
            $moneySetting
        );

        $builder->add(
            'total',
            MoneyType::class,
            $moneySetting
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tienda\Venta\Concepto',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tienda_venta_concepto';
    }


}
