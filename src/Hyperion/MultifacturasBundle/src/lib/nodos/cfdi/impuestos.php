<?php

function mf_nodo_impuestos($datos)
{
    $atributos = mf_atributos_nodo($datos, 'impuestos');
    $subnodos = '';
    if(isset($datos['retenciones']))
    {
        $subatrs = mf_atributos_nodo($datos['retenciones'], 'impuestos.retenciones');
        $subsub = '';
        foreach ($datos['retenciones'] as $idx => $retencion)
        {
            if(is_array($retencion))
            {
                $subsubatrs = mf_atributos_nodo($retencion, 'impuestos.retenciones');
                $subsub .= "<cfdi:Retencion $subsubatrs/>";
            }
        }
        $sub = "<cfdi:Retenciones $subatrs>$subsub</cfdi:Retenciones>";
        $subnodos.=$sub;
    }
    if(isset($datos['translados']))
    {
        $subatrs = mf_atributos_nodo($datos['translados'], 'impuestos.translados');
        $subsub = '';
        foreach ($datos['translados'] as $idx => $traslado)
        {
            if(is_array($traslado))
            {
                $subsubatrs = mf_atributos_nodo($traslado, 'impuestos.translados');
                $subsub .= "<cfdi:Traslado $subsubatrs/>";
            }
        }
        $sub = "<cfdi:Traslados $subatrs>$subsub</cfdi:Traslados>";
        $subnodos.=$sub;
    }
    $emisor = "<cfdi:Impuestos $atributos>$subnodos</cfdi:Impuestos>";
    return $emisor;
}