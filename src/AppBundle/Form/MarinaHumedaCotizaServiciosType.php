<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 09/10/2017
 * Time: 04:35 PM
 */

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarinaHumedaCotizaServiciosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('precioOtro', MoneyType::class, [
            'attr' => ['class' => 'esdecimal', 'readonly' => true],
            'label' => 'Otro precio (USD)',
            'currency' => 'USD',
            'divisor' => 100,
            'grouping' => true,
            'required' => false,
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                $costo = $data->getPrecio();
                $form = $event->getForm();

                $form
                    ->add('precio', EntityType::class, [
                        'class' => 'AppBundle:MarinaHumedaTarifa',
                        'label' => 'Precio',
                        'placeholder' => 'Seleccionar...',
                        'required' => false,
                        'choice_value' => 'costo',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('t')
                                ->select('t')
                                ->andWhere('t.tipo = 1')->orderBy('t.costo');
                        },
                        'choice_attr' => function ($objeto) use ($costo) {
                            return $objeto->getCosto() === $costo
                                ? ['selected' => 'selected',
                                    'class' => 'hide',
                                    'data-pies_a' => $objeto->getPiesA(),
                                    'data-pies_b' => $objeto->getPiesB(),
                                    'data-clasificacion' => $objeto->getClasificacion()
                                ]
                                : ['class' => 'hide',
                                    'data-pies_a' => $objeto->getPiesA(),
                                    'data-pies_b' => $objeto->getPiesB(),
                                    'data-clasificacion' => $objeto->getClasificacion()
                                ];
                        }
                    ])
                    ->add('precioAux', EntityType::class, [
                        'class' => 'AppBundle:MarinaHumedaTarifa',
                        'label' => 'Precio',
                        'required' => false,
                        'placeholder' => 'Seleccionar...',
                        'choice_value' => 'costo',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('t')
                                ->select('t')
                                ->andWhere('t.tipo = 2')->orderBy('t.costo');
                        },
                        'choice_attr' => function ($objeto) use ($costo) {
                            return $objeto->getCosto() === $costo
                                ? ['selected' => 'selected',
                                    'class' => 'hide',
                                    'data-pies_a' => $objeto->getPiesA(),
                                    'data-pies_b' => $objeto->getPiesB(),
                                    'data-clasificacion' => $objeto->getClasificacion()
                                ]
                                : ['class' => 'hide',
                                    'data-pies_a' => $objeto->getPiesA(),
                                    'data-pies_b' => $objeto->getPiesB(),
                                    'data-clasificacion' => $objeto->getClasificacion()
                                ];
                        }
                    ]);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MarinaHumedaCotizaServicios'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marinahumedacotizaservicios';
    }
}