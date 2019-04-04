<?php

namespace AppBundle\Form;

use AppBundle\Entity\CuentaBancaria;
use AppBundle\Entity\Pago;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class FullDataPagoType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(
        Security $security
    ) {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dolarAttributes = [
            'class' => 'esdecimal',
        ];

        if (!in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())) {
            $dolarAttributes['readonly'] = 'readonly';
        }

        $builder->add(
            'metodopago',
            ChoiceType::class,
            [
                'choices' => [
                    'Efectivo' => 'Efectivo',
                    'Transferencia' => 'Transferencia',
                    'Tarjeta de crédito' => 'Tarjeta de crédito',
                    'Tarjeta de débito' => 'Tarjeta de débito',
                    'Monedero' => 'Monedero',
                ],
                'placeholder' => 'Seleccionar...',
                'label' => false,
            ]
        );

        $builder->add(
            'divisa',
            ChoiceType::class,
            [
                'choices' => ['USD' => 'USD', 'MXN' => 'MXN'],
                'label' => false,
            ]
        );

        $builder->add(
            'cantidad',
            MoneyType::class,
            [
                'label' => false,
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'esdecimal'],
            ]
        );

        $builder->add(
            'fecharealpago',
            DateType::class,
            [
                'label' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'datepicker-solo',
                    'readonly' => true,
                ],
            ]
        );

        $builder->add(
            'dolar',
            MoneyType::class,
            [
                'label' => false,
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
                'attr' => $dolarAttributes,
            ]
        );

        $builder->add(
            'cuentabancaria',
            EntityType::class, [
                'class' => CuentaBancaria::class,
                'label' => false,
            ]
        );

        $builder->add(
            'titular',
            TextType::class,
            [
                'label' => false,
                'required' => false,
            ]
        );

        $builder->add(
            'banco',
            TextType::class,
            [
                'label' => false,
                'required' => false,
            ]
        );

        $builder->add(
            'numcuenta',
            TextType::class,
            [
                'label' => false,
                'required' => false,
            ]
        );

        $builder->add(
            'codigoseguimiento',
            TextType::class,
            [
                'label' => false,
                'required' => false,
            ]
        );

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pago::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_fulldatapago';
    }


}
