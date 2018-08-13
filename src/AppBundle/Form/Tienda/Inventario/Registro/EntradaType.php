<?php

namespace AppBundle\Form\Tienda\Inventario\Registro;

use AppBundle\Entity\Tienda\Producto;
use AppBundle\Form\EventListener\ProductoFieldListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
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
            IntegerType::class,
            [
                'empty_data' => 0,
            ]
        );

        $builder->add(
            'importe',
            MoneyType::class,
            [
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'empty_data' => 0,
            ]
        );

        $builder->add(
            'producto',
            EntityType::class,
            [
                'class' => Producto::class,
            ]
        );

        $builder->addEventSubscriber(new ProductoFieldListener($this->entityManager));
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
