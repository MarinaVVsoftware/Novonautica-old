<?php

namespace AppBundle\Form;

use AppBundle\Entity\MarinaHumedaTarifa;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MarinaHumedaTarifaType extends AbstractType
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
        $builder
            ->add('tipo',ChoiceType::class,[
                'choices' =>[
                    'Amarre' => 1,
                    'Electricidad' => 2
                ]
            ])
            ->add('costo',MoneyType::class,[
                'label' => 'Costo por día (USD)',
                'required' => false,
                'attr' => ['class' => 'esdecimal'],
                'currency' => 'USD',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('descripcion',TextareaType::class,[
                'label' => 'Descripción',
                'required' => false,
                'attr' => ['rows' => '4']
            ])
            ->add('clasificacion', ChoiceType::class, [
                'choices' => array_flip(MarinaHumedaTarifa::getClasificacionList()),
                'label' => 'Clasificación',
                'multiple' => false,
                'expanded' => true,
            ]);


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
           $configuracion = $event->getData()->getId() === null
               ? ['label' => false, 'attr' => ['class' => 'esdecimal'],'required' => false,'empty_data' => 0,'data' => 0]
               : ['label' => false, 'attr' => ['class' => 'esdecimal'],'required' => false,'empty_data' => 0];
           $event->getForm()
                ->add('piesA',TextType::class,$configuracion)
                ->add('piesB', TextType::class,$configuracion);
        });

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\MarinaHumedaTarifa',
            'constraints'        => [
                new Callback([
                    'callback' => [$this, 'correctRangeLimits'],
                ]),
                new Callback([
                    'callback' => [$this, 'uniqueRangeBoatLength'],
                ]),
            ]

        ]);
    }

    public function correctRangeLimits($data, ExecutionContextInterface $context)
    {
        if((float)$data->getPiesB() <= (float)$data->getPiesA()){
            $context
                ->buildViolation('El rango de eslora inicial es mayor o igual que el rango final.')
                ->atPath('piesA')
                ->addViolation();
        }
    }

    public function uniqueRangeBoatLength($data, ExecutionContextInterface $context)
    {
        $repetidos = $this->entityManager
            ->getRepository('AppBundle:MarinaHumedaTarifa')
            ->compruebaExistenciaRango(
                $data->getId(),
                $data->getTipo(),
                (float)$data->getPiesA(),
                (float)$data->getPiesB()
            );

        if ($data->getClasificacion() === 0 && (int)$repetidos >= 1) {
            $context
                ->buildViolation('Un valor entre el rango dado ya se encuentra registrado.')
                ->atPath('piesA')
                ->addViolation();
        }
    }



    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marinahumedatarifa';
    }
}
