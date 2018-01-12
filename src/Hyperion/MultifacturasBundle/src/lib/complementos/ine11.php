<?php
function mf_complemento_ine11($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['ine']['uri'] = 'http://www.sat.gob.mx/ine';
    $__mf_namespaces__['ine']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/ine/INE11.xsd';

    $atrs = mf_atributos_nodo($datos);

    $xml = "<ine:INE Version='1.1' $atrs>";

    if(isset($datos['Entidad']))
    {
        foreach($datos['Entidad'] as $idx => $entidad)
        {
            $atrsentidad = mf_atributos_nodo($entidad);
            $xml .= "<ine:Entidad $atrsentidad>";
            if(isset($entidad['Contabilidad']))
            {
                foreach ($entidad['Contabilidad'] as $iidx => $contabilidad)
                {
                    $atrsconta = mf_atributos_nodo($contabilidad);
                    $xml .= "<ine:Contabilidad $atrsconta/>";
                }
            }
            $xml .= "</ine:Entidad>";
        }
    }

    $xml .= "</ine:INE>";
    return $xml;
}