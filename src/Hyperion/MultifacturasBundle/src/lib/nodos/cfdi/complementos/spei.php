<?php

function mf_complemento_spei($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['spei']['uri'] = 'http://www.sat.gob.mx/spei';
    $__mf_namespaces__['spei']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/spei/spei.xsd';
    
	$atrs = mf_atributos_nodo($datos);
    $xml = "<spei:Complemento_SPEI $atrs>";
	if(isset($datos['SPEI_Tercero']))
    {
		foreach($datos['SPEI_Tercero'] as $idx =>$entidad)
		{
			if(is_array($datos['SPEI_Tercero'][$idx]) && is_int($idx))
			{
				$atrs = mf_atributos_nodo($datos['SPEI_Tercero'][$idx]);
				$xml .= "<spei:SPEI_Tercero $atrs >";
				if(isset($datos['SPEI_Tercero'][$idx]['Ordenante']))
				{
					$atrs = mf_atributos_nodo($datos['SPEI_Tercero'][$idx]['Ordenante']);
					$xml .= "<spei:Ordenante $atrs/>";
				}
				if(isset($datos['SPEI_Tercero'][$idx]['Beneficiario']))
				{
					$atrs = mf_atributos_nodo($datos['SPEI_Tercero'][$idx]['Beneficiario']);
					$xml .= "<spei:Beneficiario $atrs/>";
				}
				$xml .= "</spei:SPEI_Tercero>";
			}	
		}
    }
    $xml .= "</spei:Complemento_SPEI>";
    return $xml;
}