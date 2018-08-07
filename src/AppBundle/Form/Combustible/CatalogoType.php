<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 07/08/2018
 * Time: 05:11 PM
 */

namespace AppBundle\Form\Combustible;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CatalogoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,[
                'label' => 'Combustible'
            ])
            ->add('precio', MoneyType::class,[
                'attr' => ['class' => 'esdecimal'],
                'currency' => 'MXN',
                'divisor' => 100,
            ])
            ->add('cuotaIesps', TextType::class,[
                'label' => 'Cuota IESPS',
                'attr' => ['class' => 'esdecimal']
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Combustible\Catalogo'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_combustible_catalogo';
    }

}