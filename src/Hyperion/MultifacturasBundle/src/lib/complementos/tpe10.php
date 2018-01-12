<?php

function mf_complemento_tpe10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['tpe']['uri'] = 'http://www.sat.gob.mx/TuristaPasajeroExtranjero';
    $__mf_namespaces__['tpe']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/TuristaPasajeroExtranjero/TuristaPasajeroExtranjero.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<tpe:TuristaPasajeroExtranjero version='1.0' $atrs>";
	if(isset($datos['datosTransito']))
	{
		$atrs = mf_atributos_nodo($datos['datosTransito']);
		$xml .= "<tpe:datosTransito $atrs/>";
	}
	$xml .= "</tpe:TuristaPasajeroExtranjero>";
    return $xml;
}
