<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 08/10/2018
 * Time: 11:17 AM
 */

namespace AppBundle\Form;



use AppBundle\Form\Gasto\ConceptoType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotNull;

class GastoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('empresa',EntityType::class,[
                'class' => 'AppBundle\Entity\Contabilidad\Facturacion\Emisor',
                'query_builder' => function (EntityRepository $er){
                    $query = $er->createQueryBuilder('e');
                    $views = [];
                    foreach ($this->security->getUser()->getRoles() as $role){
                        if(strpos($role, 'ROLE_ADMIN')===0){
                            return $query;
                        }
                        if (strpos($role, 'VIEW_GASTO') === 0) {
                            $views[] = explode('_', $role)[3];
                        }
                    }
                    return $query->where($query->expr()->in('e.id',$views));
                },
                'constraints' => [new NotNull(['message' => 'Por favor selecciona una empresa'])]
            ])
            ->add('total',MoneyType::class,[
                'attr' => ['class' => 'esdecimal','readonly' => 'readonly'],
                'label' => 'Gran Total',
                'currency' => 'MXN',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('conceptos',CollectionType::class,[
                'label' => false,
                'entry_type' => ConceptoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Gasto',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_gasto';
    }
}