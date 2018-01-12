<?php

function mf_complemento_ventavehiculos11($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['ventavehiculos']['uri'] = 'http://www.sat.gob.mx/ventavehiculos';
    $__mf_namespaces__['ventavehiculos']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/ventavehiculos/ventavehiculos11.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<ventavehiculos:VentaVehiculos version='1.1' $atrs>";
	
	if(isset($datos['InformacionAduanera']))
    {
		foreach($datos['InformacionAduanera'] as $idx2 => $entidad2)
		{
			if(is_array($datos['InformacionAduanera'][$idx2]))
			{
				$atrs = mf_atributos_nodo($datos['InformacionAduanera'][$idx2]);
				$xml .= "<ventavehiculos:InformacionAduanera $atrs/>";
			}
		}
    }
	if(isset($datos['Parte']))
    {
		foreach($datos['Parte'] as $idx2 => $entidad2)
		{
			$atrs = mf_atributos_nodo($datos['Parte'][$idx2]);
			$xml .= "<ventavehiculos:Parte $atrs>";
			if(isset($entidad2['InformacionAduanera']))
			{
				foreach($entidad2['InformacionAduanera'] as $idx => $entidad)
				{
					if(is_array($entidad2['InformacionAduanera'][$idx]))
					{
						$atrs = mf_atributos_nodo($entidad2['InformacionAduanera'][$idx]);
						$xml .= "<ventavehiculos:InformacionAduanera $atrs/>";
					}
				}
			}
			$xml .= "</ventavehiculos:Parte>";
		}
    }
	$xml .= "</ventavehiculos:VentaVehiculos>";
    return $xml;
}
