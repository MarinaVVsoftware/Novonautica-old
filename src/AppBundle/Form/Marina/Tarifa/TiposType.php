<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 2019-02-06
 * Time: 12:10
 */

namespace AppBundle\Form\Marina\Tarifa;


use AppBundle\Entity\Marina\Tarifa\Tipo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class TiposType extends AbstractType
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
        $tipos = $this->manager->getRepository(Tipo::class)->findAll();

        $builder->add(
            'tipos',
            CollectionType::class,
            [
                'entry_type' => TipoType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'label' => 'Tipos de tarifas',
                'entry_options' => [
                    'label' => false,
                ],
                'data' => $tipos
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_marina_tarifa_tipo';
    }
}
