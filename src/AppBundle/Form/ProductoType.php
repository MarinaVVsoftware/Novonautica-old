<?php

namespace AppBundle\Form;

//use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class ProductoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,[
                'required' => false
            ])
            ->add('codigo',TextType::class,[
                'label' => 'Código',
                'required' => false
            ])
            ->add('descripcion',TextareaType::class,[
                'label' => 'Descripción',
                'required' => false
            ])
            ->add('precio',TextType::class,[
                'required' => false,
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('cantidad',TextType::class,[
                'required' => false,
                'attr' => ['class' => 'esdecimal']
            ])
            ->add('unidad',TextType::class,[
                'required' => false
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Producto'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_producto';
    }


}
