<?php

namespace AppBundle\Repository\Gasto;

/**
 * ConceptoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConceptoRepository extends \Doctrine\ORM\EntityRepository
{
    public function getReporteGastos($idempresa,$idconcepto,$inicio,$fin)
    {
        $resultado = [];
        $granTotal = 0;
        $condicion_empresa = '';
        $condicion_concepto = '';
        if($idempresa !== 0){$condicion_empresa = ' AND emisor.id = :idEmisor ';}
        if($idconcepto !== 0){$condicion_concepto = ' AND servicio.id = :idConcepto ';}
        $qry = 'SELECT concepto, servicio, gasto, emisor '.
            ' FROM AppBundle:Gasto\Concepto concepto '.
            ' LEFT JOIN concepto.servicio servicio '.
            ' LEFT JOIN concepto.gasto gasto '.
            ' LEFT JOIN gasto.empresa emisor '.
            ' WHERE gasto.fecha BETWEEN :inicio AND :fin '.
            $condicion_empresa.
            $condicion_concepto.
            ' ORDER BY gasto.fecha ASC';
        $conceptos = $this->getEntityManager()->createQuery($qry);
        $conceptos->setParameter('inicio',$inicio);
        $conceptos->setParameter('fin',date('Y-m-d H:i:s',strtotime('+23 hour +59 minutes +59 seconds',strtotime($fin))));
        if($idempresa !== 0){$conceptos->setParameter('idEmisor',$idempresa);}
        if($idconcepto !== 0){$conceptos->setParameter('idConcepto',$idconcepto);}
        foreach ($conceptos->getArrayResult() as $concepto){
            array_push($resultado,[
                'fecha' => $concepto['gasto']['fecha']->format('d/m/Y'),
                'empresa' => $concepto['gasto']['empresa']['nombre'],
                'concepto' => $concepto['servicio']['nombre'],
                'total' => $this->esMoneda($concepto['total']),
            ]);
            $granTotal+=$concepto['total'];
        }
        array_push($resultado,[
           'fecha' => '',
           'empresa' => '',
           'concepto' => '',
           'total' => $this->esMoneda($granTotal)
        ]);
        return $resultado;
    }

    function esMoneda($valor){
        return '$'.number_format($valor/100,2);
    }
}