<?php

function mf_nodo_receptor($datos)
{
    global $__mf_constantes__;
    if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.2')
    {
        $atributos = mf_atributos_nodo($datos, 'receptor');
        $subnodos = '';
        if(isset($datos['Domicilio']))
        {
            $subatrs = mf_atributos_nodo($datos['Domicilio'], 'receptor.Domicilio');
            $sub = "<cfdi:Domicilio $subatrs />";
            $subnodos.=$sub;
        }
        $receptor = "<cfdi:Receptor $atributos>$subnodos</cfdi:Receptor>";
    }
    if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.3')
    {
        $atributos = mf_atributos_nodo($datos, 'receptor');
        $receptor = "<cfdi:Receptor $atributos/>";
    }
    return $receptor;
}