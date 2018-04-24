<?php
/**
 * Created by PhpStorm.
 * User: Luiz
 * Date: 23/04/2018
 * Time: 03:53 PM
 */

namespace AppBundle\Security;


use AppBundle\Entity\OrdenDeTrabajo;
use AppBundle\Entity\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ODTVoter extends Voter
{
    const CREATE = 'ROLE_ODT_CREATE';
    const PAY = 'ROLE_ODT_PAGO';
    const ACTIVITY = 'ROLE_ODT_ACTIVIDAD';
    const EDIT = 'ROLE_ODT_CONTRATISTA_EDIT';
    const DELETE = 'ROLE_ODT_DELETE';
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
        if (!in_array($attribute, [self::CREATE, self::PAY, self::ACTIVITY, self::EDIT, self::DELETE])) {
            return false;
        }
        if (!$subject instanceof OrdenDeTrabajo) {
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
            case self::CREATE:
                return $this->canCreate($user);
                break;
            case self::PAY:
                return $this->canPay($user);
                break;
            case self::ACTIVITY:
                return $this->canActivity($user);
                break;
            case self::EDIT:
                return $this->canEdit($user);
                break;
            case self::DELETE:
                return $this->canDelete($user);
                break;
        }

        throw new \LogicException('Olvidaste validar la acción?');
    }
    private function canCreate(Usuario $usuario)
    {
        if (!in_array(self::CREATE, $usuario->getRoles())) {
            return false;
        }
        return true;
    }
    private function canPay(Usuario $usuario)
    {
        if (!in_array(self::PAY, $usuario->getRoles())) {
            return false;
        }
        return true;
    }
    private function canActivity(Usuario $usuario)
    {
        if (!in_array(self::ACTIVITY, $usuario->getRoles())) {
            return false;
        }
        return true;
    }
    private function canEdit(Usuario $usuario)
    {
        if (!in_array(self::EDIT, $usuario->getRoles())) {
            return false;
        }
        return true;
    }
    private function canDelete(Usuario $usuario)
    {
        if (!in_array(self::EDIT, $usuario->getRoles())) {
            return false;
        }
        return true;
    }
}