<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 31/10/2018
 * Time: 03:24 PM
 */

namespace AppBundle\Form;


use AppBundle\Form\Solicitud\ConceptoType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotNull;

class SolicitudType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
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
                    if (strpos($role, 'VIEW_SOLICITUD') === 0) {
                        $views[] = explode('_', $role)[3];
                    }
                }
                return $query->where($query->expr()->in('e.id',$views));
            },
            'constraints' => [new NotNull(['message' => 'Por favor selecciona una empresa'])]
            ])
            ->add('conceptos',CollectionType::class,[
                'label' => false,
                'entry_type' => ConceptoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ])
            ->add('notaSolicitud',TextareaType::class,[
                'required' => false,
                'attr' => ['rows' => 5, 'class' => 'info-input'],
                'label' => 'Notas'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Solicitud',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_solicitud';
    }
}