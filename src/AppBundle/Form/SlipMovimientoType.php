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
            ->add('marinahumedacotizacion', EntityType::class, [
                'class' => 'AppBundle:MarinaHumedaCotizacion',
                'label' => 'Cotización Marina Húmeda',
                'placeholder' => 'Seleccionar...',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('mhc');

                    return $qb
                        ->select('mhc', 'servicios')
                        ->leftJoin('mhc.mhcservicios', 'servicios', 'slipmovimiento')
                        ->leftJoin('mhc.slipmovimiento', 'slipmovimiento')
                        ->andWhere(
                            $qb->expr()->eq('servicios.tipo', 1),
                            $qb->expr()->eq('mhc.validacliente', 2),
                            $qb->expr()->gt('mhc.fechaSalida', 'CURRENT_DATE()'),
                            $qb->expr()->isNull('mhc.slip'),
                            $qb->expr()->isNull('slipmovimiento.id')
                        )
                        ->orderBy('mhc.folio', 'DESC');
                },
                'choice_label' => function ($cotizacion) {
                    /** @var MarinaHumedaCotizacion $cotizacion */
                    $f = $cotizacion->getFolio().($cotizacion->getFoliorecotiza() ? '-'.$cotizacion->getFoliorecotiza() : '');

                    return "#{$f}, Ship: {$cotizacion->getBarco()}";
                },
                'choice_attr' => function ($mhc) {
                    return [
                        'data-eslora' => $mhc->getBarco()->getEslora(),
                        'data-llegada' => $mhc->getFechaLlegada()->format('d-m-Y'),
                        'data-salida' => $mhc->getFechaSalida()->format('d-m-Y'),
                    ];
                },
            ])
            ->add('slip', EntityType::class, [
                'class' => 'AppBundle:Slip',
                'label' => 'Slip',
                'placeholder' => 'Seleccionar...',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SlipMovimiento',
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
