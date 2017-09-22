<?php

namespace AppBundle\Form;

use AppBundle\Entity\Rol;
use ClassesWithParents\E;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UsuarioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
            ])
            ->add('correo', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('username', TextType::class, [
                'label' => 'Nombre Usuario'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password'
            ])
            ->add('estatus',null,[
                'label' => ' '
            ])
//            ->add('idrol')
            ->add('rol', EntityType::class, [
                'label' => 'Nivel',
                'class' => Rol::class
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();
            if (!$user || null === $user->getId()) {
                $form->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Contraseña'],
                    'second_options' => ['label' => 'Repetir contraseña']
                ]);
            } else {
                $form->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'required' => false,
                    'first_options' => ['label' => 'Contraseña', 'empty_data' => $user->getPassword()],
                    'second_options' => ['label' => 'Repetir contraseña', 'empty_data' => $user->getPassword()]
                ]);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Usuario'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_usuario';
    }


}
