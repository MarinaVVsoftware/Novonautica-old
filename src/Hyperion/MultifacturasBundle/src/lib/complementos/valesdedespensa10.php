<?php

function mf_complemento_valesdedespensa10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['valesdedespensa']['uri'] = 'http://www.sat.gob.mx/valesdedespensa';
    $__mf_namespaces__['valesdedespensa']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/valesdedespensa/valesdedespensa.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<valesdedespensa:ValesDeDespensa version='1.0' $atrs>";
	if(isset($datos['Conceptos']))
	{
		$atrs = mf_atributos_nodo($datos['Conceptos']);
		$xml .= "<valesdedespensa:Conceptos $atrs>";
		foreach($datos['Conceptos'] as $idx =>$entidad)
		{
			if(is_array($datos['Conceptos'][$idx]))
			{
				$atrs = mf_atributos_nodo($entidad);
				$xml .= "<valesdedespensa:Concepto $atrs/>";
			}
		}
		$xml .= "</valesdedespensa:Conceptos>";
	}
	$xml .= "</valesdedespensa:ValesDeDespensa>";
    return $xml;
}
