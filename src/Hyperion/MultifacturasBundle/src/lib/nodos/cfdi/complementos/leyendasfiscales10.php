<?php

function mf_complemento_leyendasfiscales10($datos)
{
	// Variable para los namespaces xml
	global $__mf_namespaces__;
	$__mf_namespaces__['leyendasFisc']['uri'] = 'http://www.sat.gob.mx/leyendasFiscales';
	$__mf_namespaces__['leyendasFisc']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/leyendasFiscales/leyendasFisc.xsd';

	$atrs = mf_atributos_nodo($datos);
    $xml = "<leyendasFisc:LeyendasFiscales version='1.0' $atrs>";
	foreach($datos as $idx =>$entidad)
	{
		if(is_array($datos[$idx]))
		{
			$atrs = mf_atributos_nodo($entidad);
			$xml .= "<leyendasFisc:Leyenda $atrs/>";
		}
	}
    $xml .= "</leyendasFisc:LeyendasFiscales>";
    return $xml;
}
