<?php

function mf_complemento_ecc11($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['ecc11']['uri'] = 'http://www.sat.gob.mx/EstadoDeCuentaCombustible';
    $__mf_namespaces__['ecc11']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/EstadoDeCuentaCombustible/ecc11.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<ecc11:EstadoDeCuentaCombustible Version='1.1' $atrs>";
	if(isset($datos['Conceptos']))
	{
		$atrs = mf_atributos_nodo($datos['Conceptos']);
		$xml .= "<ecc11:Conceptos $atrs>";
		foreach($datos['Conceptos'] as $idx =>$entidad)
		{
			if(is_array($datos['Conceptos'][$idx]))
			{
				$atrs = mf_atributos_nodo($entidad);
				$xml .= "<ecc11:ConceptoEstadoDeCuentaCombustible $atrs>";
				if(isset($entidad['Traslados']))
				{
					$atrs = mf_atributos_nodo($entidad['Traslados']);
					$xml .= "<ecc11:Traslados $atrs>";
					foreach($entidad['Traslados'] as $idx2 =>$det)
					{
						if(is_array($entidad['Traslados'][$idx2]))
						{
							$atrs = mf_atributos_nodo($det);
							$xml .= "<ecc11:Traslado $atrs/>";
						}
					}
					$xml .= "</ecc11:Traslados>";
				}
				$xml .= "</ecc11:ConceptoEstadoDeCuentaCombustible>";
			}
		}
		$xml .= "</ecc11:Conceptos>";
	}
	$xml .= "</ecc11:EstadoDeCuentaCombustible>";
    return $xml;
}
