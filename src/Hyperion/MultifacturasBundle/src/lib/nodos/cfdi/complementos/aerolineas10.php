<?php

function mf_complemento_aerolineas10($datos)
{
    // Se agregan los namespaces
    global $__mf_namespaces__;
    $__mf_namespaces__['aerolineas']['uri'] = 'http://www.sat.gob.mx/aerolineas';
    $__mf_namespaces__['aerolineas']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/aerolineas/aerolineas.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<aerolineas:Aerolineas Version='1.0' $atrs>";
	
	if(isset($datos['OtrosCargos']))
    {
		$atrsentidad = mf_atributos_nodo($datos['OtrosCargos']);
		$xml .= "<aerolineas:OtrosCargos $atrsentidad>";
		foreach($datos['OtrosCargos'] as $idx =>$entidad)
		{
			if(is_array($datos['OtrosCargos'][$idx]))
			{
				$atrs = mf_atributos_nodo($entidad);
				$xml .= "<aerolineas:Cargo $atrs />";
			}	
		}
		$xml .= "</aerolineas:OtrosCargos>";
    }
	
    $xml .= "</aerolineas:Aerolineas>";
    return $xml;
}
