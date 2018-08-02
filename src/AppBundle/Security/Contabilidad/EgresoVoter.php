<?php
/**
 * User: inrumi
 * Date: 8/2/18
 * Time: 12:56
 */

namespace AppBundle\Security\Contabilidad;


use AppBundle\Entity\Contabilidad\Egreso;
use AppBundle\Entity\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EgresoVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    /**
     * @var AccessDecisionManagerInterface
     */
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
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Egreso) {
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

        if (!$user instanceof Usuario) {
            return false;
        }

        if ($this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        /** @var Egreso $subject */
        $egreso = $subject;
        $empresas = [];

        foreach ($user->getRoles() as $role) {
            if (strpos($role, 'VIEW_EGRESO') === 0) {
                $empresas[] = explode('_', $role)[3];
            }
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($egreso, $user, $empresas);
            case self::EDIT:
                return $this->canEdit($egreso, $user, $empresas);
        }
    }


    private function canView(Egreso $egreso, Usuario $user, array $empresas) {
        if ($this->canEdit($egreso, $user, $empresas)) {
            return true;
        }

        if (in_array($egreso->getEmpresa()->getId(), $empresas)) {
            return true;
        }

        return false;
    }

    private function canEdit(Egreso $egreso, Usuario $user, array $empresas)
    {
        if (
            in_array('EGRESO_EDIT', $user->getRoles()) &&
            in_array($egreso->getEmpresa()->getId(), $empresas)
        ) {
            return true;
        }

        return false;
    }
}
