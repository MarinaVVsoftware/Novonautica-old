<?php

function mf_complemento_pagos10($datos)
{
	// Variable para los namespaces xml
	global $__mf_namespaces__;
	$__mf_namespaces__['pago10']['uri'] = 'http://www.sat.gob.mx/Pagos';
	$__mf_namespaces__['pago10']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd';

	$atrs = mf_atributos_nodo($datos);
    $xml = "<pago10:Pagos Version='1.0' $atrs>";
	if(isset($datos['Pagos']))
    {
		foreach($datos['Pagos'] as $idx =>$entidad)
		{
			if(is_array($datos['Pagos'][$idx]))
			{
				$atrs = mf_atributos_nodo($datos['Pagos'][$idx]);
				$xml .= "<pago10:Pago $atrs >";
				if(isset($entidad['Impuestos']))
				{
					foreach($entidad['Impuestos'] as $idx2 =>$subentidad)
					{
						if(is_array($entidad['Impuestos'][$idx2]))
						{
							$atrs = mf_atributos_nodo($subentidad);
							$xml .= "<pago10:Impuestos $atrs>";
							if(isset($subentidad['Retenciones']))
							{
								$atrs = mf_atributos_nodo($subentidad['Retenciones']);
								$xml .= "<pago10:Retenciones $atrs>";
								foreach($subentidad['Retenciones'] as $idx3 =>$subsub)
								{
									if(is_array($subentidad['Retenciones'][$idx3]))
									{
										$atrs = mf_atributos_nodo($subsub);
										$xml .= "<pago10:Retencion $atrs/>";
									}
								}
								$xml .= "</pago10:Retenciones>";
							}
							if(isset($subentidad['Traslados']))
							{
								$atrs = mf_atributos_nodo($subentidad['Traslados']);
								$xml .= "<pago10:Traslados $atrs>";
								foreach($subentidad['Traslados'] as $idx3 =>$subsub)
								{
									if(is_array($subentidad['Traslados'][$idx3]))
									{
										$atrs = mf_atributos_nodo($subsub);
										$xml .= "<pago10:Traslado $atrs/>";
									}
								}
								$xml .= "</pago10:Traslados>";
							}
							$xml .= "</pago10:Impuestos>";
						}
					}
				}
				if(isset($entidad['DoctoRelacionado']))
				{
					foreach($entidad['DoctoRelacionado'] as $idx2 => $entidad2)
					{
						if(is_array($entidad['DoctoRelacionado'][$idx2]))
						{
							$atrs = mf_atributos_nodo($entidad2);
							$xml .= "<pago10:DoctoRelacionado $atrs/>";
						}
					}
				}
				$xml .= "</pago10:Pago>";
			}	
		}
	}
    $xml .= "</pago10:Pagos>";
    return $xml;
}