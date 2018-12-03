<?php
/**
 * Created by PhpStorm.
 * User: Holograma
 * Date: 19/03/2018
 * Time: 10:37 PM
 */

namespace AppBundle\Form\Astillero;


use AppBundle\Entity\Astillero\Contratista;
use AppBundle\Entity\Astillero\Producto;
use AppBundle\Entity\Astillero\Proveedor;
use AppBundle\Validator\Constraints\ProductHaveStockValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class ContratistaType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cotizacionInicial',TextType::class,[
                'label' => 'Descripción'
            ])
            ->add('cantidad', TextType::class,[
                'attr' => ['readonly' => true]
            ])
            ->add('precio',MoneyType::class,[
                'label' => 'Precio Trabajador (MXN)',
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'esdecimal preciocontratista','readonly'=>true]
            ])
            ->add('porcentajevv',TextType::class,[
                'label' => '% V&V',
                'attr' => ['class'=>'porcentajevv','readonly'=>true]
            ])
            ->add('utilidadvv',MoneyType::class,[
                'label' => 'Utilidad V&V (MXN)',
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'esdecimal utilidadvv','readonly'=>true]
            ])
            ->add('preciovv',MoneyType::class,[
                'label' => 'Precio V&V (MXN)',
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
                'attr' => ['class' => 'esdecimal preciovv','readonly'=>true]
            ])
            ->add('proveedor',EntityType::class,[
                'class' => 'AppBundle\Entity\Astillero\Proveedor',
                'label' => 'Trabajador',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class'=>'buscaproveedor lista-trabajadores'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        // Empresa 5 = Astillero
                        ->where('t.empresa = 5')
                        ->orderBy('t.nombre', 'ASC');
                },
                'choice_attr' => function(Proveedor $proveedor, $key, $index) {
                    return ['data-trabajador' => $proveedor->getProveedorcontratista(),
                            'data-id' => $proveedor->getId()
                    ];
                },

            ])
        ;

        /**
         * UNMAPPED FIELDS
         */

        $builder->add(
            'producto',
            HiddenType::class
        );

        /**
         * DATA TRANSFORMER
         */

        $builder->get('producto')
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($value) {
                        return $value;
                    },
                    function ($value) {
                        if (!$value) {
                            return null;
                        }

                        return $this->manager->getRepository(Producto::class)->find($value);
                    }
                )
            );
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contratista::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillerocontratista';
    }
}
