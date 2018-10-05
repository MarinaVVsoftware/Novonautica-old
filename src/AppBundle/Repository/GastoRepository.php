<?php

namespace AppBundle\Repository;

/**
 * GastoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GastoRepository extends \Doctrine\ORM\EntityRepository
{
    function compruebaRol($roles,$idEmpresa){
        $coincide = false;
        foreach ($roles as $role){
            if(strpos($role, 'ROLE_ADMIN')===0){
                $coincide = true;
            }else if (strpos($role, 'VIEW_GASTO') === 0) {
                if((int)explode('_', $role)[3] === $idEmpresa){
                    $coincide = true;
                }
            }
        }
        return $coincide;
    }
}
