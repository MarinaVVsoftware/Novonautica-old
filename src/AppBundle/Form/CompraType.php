<?php
/**
 * Created by PhpStorm.
 * User: Holograma
 * Date: 02/11/2018
 * Time: 07:48 PM
 */

namespace AppBundle\Form;

use AppBundle\Form\Compra\ConceptoType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;


class CompraType extends AbstractType
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
        $formatoMoney = [
            'currency' => 'MXN',
            'divisor' => 100,
            'grouping' => true,
            'data' => 0,
            'attr' => ['class' => 'esdecimal','readonly' => 'readonly'],
            'label' => false
        ];
        $builder
            ->add('solicitud',EntityType::class,[
                'class' => 'AppBundle\Entity\Solicitud',
                'placeholder' => 'Selecionar...',
                //'constraints' => [new NotNull(['message' => 'Por favor selecciona una solicitud'])]
                'choice_label' => function ($solicitud) {
                    return 'Folio: '.$solicitud->getFolio().' - '.$solicitud->getFecha()->format('d/m/Y');
                }
            ])
            ->add('conceptos',CollectionType::class,[
                'label' => false,
                'entry_type' => ConceptoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ])
            ->add('nota',TextareaType::class,[
                'required' => false,
                'attr' => ['rows' => 5, 'class' => 'info-input'],
                'label' => 'Notas'
            ])
            ->add('iva',TextType::class,[
                'attr' => ['class' => 'esdecimal'],
            ])
            ->add('subtotal',MoneyType::class,$formatoMoney)
            ->add('ivatotal',MoneyType::class,$formatoMoney)
            ->add('total',MoneyType::class,$formatoMoney);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Compra',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_compra';
    }
}