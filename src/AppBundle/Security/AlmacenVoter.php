<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 12/11/2018
 * Time: 12:57 PM
 */

namespace AppBundle\Security;

use AppBundle\Entity\Solicitud;
use AppBundle\Entity\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AlmacenVoter extends Voter
{
    const VALIDAR = 'ALMACEN_VALIDAR';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::VALIDAR])) {
            return false;
        }

        if (!$subject instanceof Solicitud) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        switch ($attribute) {
            case self::VALIDAR:
                return $this->canValidar($user);
                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canValidar(Usuario $usuario)
    {
        if (!in_array(self::VALIDAR, $usuario->getRoles())) {
            return false;
        }
        return true;
    }
}
