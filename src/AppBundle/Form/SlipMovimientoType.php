<?php

namespace AppBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Slip;
use AppBundle\Entity\MarinaHumedaCotizacion;

class SlipMovimientoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('fechaLlegada')
//            ->add('fechaSalida')
//            ->add('estatus')
//            ->add('createdAt')
            ->add('marinahumedacotizacion',EntityType::class,[
                'class' => 'AppBundle:MarinaHumedaCotizacion',
                'label' => 'Cotización Marina Húmeda',
                'placeholder' => 'Seleccionar...',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('mhc')
                        ->select('mhc', 'servicios')
                        ->join('mhc.mhcservicios','servicios','slipmovimiento')
                        ->leftJoin('mhc.slipmovimiento','slipmovimiento')
                        ->where($er->createQueryBuilder('mhc')
                                    ->expr()->andX(
                                        $er->createQueryBuilder('mhc')->expr()->neq('servicios.tipo', '3'),
                                        $er->createQueryBuilder('mhc')->expr()->eq('mhc.validacliente', '2'),
                                        $er->createQueryBuilder('mhc')->expr()->isNull('slipmovimiento.id')
                            )
                        )
                        ->orderBy('mhc.folio', 'DESC')
                        ;
                },
                'choice_attr' => function ($mhc) {
                    /** @var MarinaHumedaCotizacion $mhc */
                    return ['data-eslora' => $mhc->getBarco()->getEslora(),
                        'data-llegada' => date('Y-m-d', strtotime($mhc->getFechaLlegada()->format('Y-m-d'))),
                        'data-salida' => date('Y-m-d', strtotime($mhc->getFechaSalida()->format('Y-m-d')))];
                }
            ])->add('slip',EntityType::class,[
                'class' => 'AppBundle:Slip',
                'label' => 'Slip',
                'placeholder' => 'Seleccionar...',
                'choice_attr' => function ($slip) {
                    /** @var Slip $slip */
                    return ['data-eslora' => $slip->getPies(),'style'=>'display:none;'];
                }
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SlipMovimiento'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_slipmovimiento';
    }


}
