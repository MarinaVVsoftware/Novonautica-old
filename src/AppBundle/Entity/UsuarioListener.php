<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 3/14/18
 * Time: 14:24
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioListener
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function prePersist(Usuario $usuario)
    {
        $this->encodePassword($usuario);
    }

    public function preUpdate(Usuario $usuario, PreUpdateEventArgs $eventArgs)
    {
        $this->encodePassword($usuario);

        $em = $eventArgs->getEntityManager();
        $meta = $em->getClassMetadata(get_class($usuario));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $usuario);
    }

    /**
     * @param Usuario $entity
     */
    private function encodePassword(Usuario $entity)
    {
        if (!$entity->getPlainPassword()) {
            return;
        }

        $encodedPassword = $this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword());
        $entity->setPassword($encodedPassword);
    }
}