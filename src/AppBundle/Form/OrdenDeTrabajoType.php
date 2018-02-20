<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdenDeTrabajoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('astilleroCotizacion',EntityType::class,[
                'class' => 'AppBundle\Entity\AstilleroCotizacion',
                'label' => 'Cotización',
                'placeholder' => 'Seleccionar...',
                'attr' => ['class' => 'select-buscador buscarfolio'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('ac')
                        ->select('ac')
                        ->andWhere('ac.validacliente = 2')
                        ->andWhere('ac.estatus = 1')
                        ->orderBy('ac.folio');
                }
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrdenDeTrabajo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_ordendetrabajo';
    }


}
