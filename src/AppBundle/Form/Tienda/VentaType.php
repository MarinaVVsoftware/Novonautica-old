<?php

namespace AppBundle\Form\Tienda;

use AppBundle\Entity\Cliente;
use AppBundle\Entity\Tienda\Venta;
use AppBundle\Form\Tienda\Venta\ConceptoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class VentaType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $moneySetting = [
            'label' => false,
            'currency' => 'MXN',
            'divisor' => 100,
            'grouping' => true,
            'data' => 0,
        ];

        $builder->add(
            'descuento',
            MoneyType::class,
            $moneySetting
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

        $builder->add(
            'conceptos',
            CollectionType::class,
            [
                'label' => false,
                'entry_type' => ConceptoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
            ]
        );

        /*
         * Event listeners
         */

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $this->createClienteField($event->getForm(), $event->getData()->getCliente());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                $cliente = array_key_exists('cliente', $data)
                    ? $this->entityManager->getRepository(Cliente::class)->find($data['cliente'])
                    : null;

                $this->createClienteField($event->getForm(), $cliente);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Venta::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tienda_venta';
    }

    private function createClienteField(FormInterface $form, Cliente $cliente = null)
    {
        $form->add(
            'cliente',
            EntityType::class,
            [
                'label' => false,
                'class' => Cliente::class,
                'choice_label' => 'nombre',
                'choices' => null === $cliente ? [] : [$cliente],
                'placeholder' => 'Seleccione un cliente',
                'required' => true,
                'attr' => ['required' => 'required'],
                'constraints' => [
                    new NotNull(['message' => 'Por favor selecciona un cliente']),
                    new NotBlank(['message' => 'Por favor selecciona un cliente']),
                ],
            ]
        );
    }
}
