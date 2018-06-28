<?php

namespace AppBundle\Form\Tienda\Inventario\Registro;

use AppBundle\Entity\Tienda\Producto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntradaType extends AbstractType
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'cantidad',
            NumberType::class,
            [
                'data' => 0,
            ]
        );

        $builder->add(
            'importe',
            MoneyType::class,
            [
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'data' => 0,
            ]
        );

        $formModifier = function (FormInterface $form, $productoId = null) {
            $productoRepository = $this->entityManager->getRepository(Producto::class);
            $productos = null === $productoId ? [] : [$productoRepository->find($productoId)];

            $form->add(
                'producto',
                EntityType::class,
                [
                    'class' => Producto::class,
                    'placeholder' => 'Seleccione un producto',
                    'choices' => $productos
                ]
            );
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $form = $event->getForm();

                $formModifier($form);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                $productoId = array_key_exists('producto', $data) ? $data['producto'] : null;

                $formModifier($event->getForm(), $productoId);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tienda\Inventario\Registro\Entrada',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_tienda_inventario_registro_entrada';
    }


}
