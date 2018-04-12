<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 11/04/2018
 * Time: 11:38 AM
 */

namespace AppBundle\Form\Astillero\Contratista;


use AppBundle\Entity\Usuario;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActividadType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,[
                'label' => 'Actividad'
            ])
            ->add('inicio',DateType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker input-calendario',
                    'readonly' => true
                    ],
                'format' => 'yyyy-MM-dd',
//                'data' => new \DateTime('now')
            ])
            ->add('fin',DateType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker input-calendario',
                    'readonly' => true],
                'format' => 'yyyy-MM-dd',
//                'data' => new \DateTime('now')
            ])
            ->add('notas',TextareaType::class,[
                'label' => 'Notas',
                'required'=>false,
                'attr' =>['rows'=> 3]
            ])
            ->add('responsable', EntityType::class, [
                'class' => 'AppBundle:Usuario',
                'choice_value' => function ($nombre) {
                    return $nombre;
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :elrol')
                        ->orderBy('u.nombre', 'ASC')
                        ->setParameter('elrol','%ROLE_ASTILLERO_RESPONSABLE%')
                        ;
                },
            ])
            ->add('porcentaje', TextType::class, [
                'required'=>false,
                'attr' => ['class' => 'esdecimal limite100'],
                'empty_data' => 0,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Astillero\Contratista\Actividad'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_astillero_contratista_actividad';
    }
}