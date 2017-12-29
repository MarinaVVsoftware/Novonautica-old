<?php

function mf_complemento_vehiculousado10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['vehiculousado']['uri'] = 'http://www.sat.gob.mx/vehiculousado';
    $__mf_namespaces__['vehiculousado']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/vehiculousado/vehiculousado.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<vehiculousado:VehiculoUsado Version='1.0' $atrs>";
	
	if(isset($datos['InformacionAduanera']))
    {
		foreach($datos['InformacionAduanera'] as $idx2 => $entidad2)
		{
			if(is_array($datos['InformacionAduanera'][$idx2]))
			{
				$atrs = mf_atributos_nodo($datos['InformacionAduanera'][$idx2]);
				$xml .= "<vehiculousado:InformacionAduanera $atrs/>";
			}
		}
    }
	$xml .= "</vehiculousado:VehiculoUsado>";
    return $xml;
}
