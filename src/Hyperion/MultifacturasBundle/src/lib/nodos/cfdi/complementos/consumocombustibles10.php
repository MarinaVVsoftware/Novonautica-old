<?php

function mf_complemento_consumocombustibles10($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['consumodecombustibles']['uri'] = 'http://www.sat.gob.mx/consumodecombustibles';
    $__mf_namespaces__['consumodecombustibles']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/consumodecombustibles/consumodecombustibles.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<consumodecombustibles:ConsumoDeCombustibles version='1.0' $atrs>";
	if(isset($datos['Conceptos']))
	{
		$atrs = mf_atributos_nodo($datos['Conceptos']);
		$xml .= "<consumodecombustibles:Conceptos $atrs>";
		foreach($datos['Conceptos'] as $idx =>$entidad)
		{
			if(is_array($datos['Conceptos'][$idx]))
			{
				$atrs = mf_atributos_nodo($entidad);
				$xml .= "<consumodecombustibles:ConceptoConsumoDeCombustibles $atrs>";
				if(isset($entidad['Determinados']))
				{
					$atrs = mf_atributos_nodo($entidad['Determinados']);
					$xml .= "<consumodecombustibles:Determinados>";
					foreach($entidad['Determinados'] as $idx2 =>$det)
					{
						if(is_array($entidad['Determinados'][$idx2]))
						{
							$atrs = mf_atributos_nodo($det);
							$xml .= "<consumodecombustibles:Determinado $atrs/>";
						}
					}
					$xml .= "</consumodecombustibles:Determinados>";
				}
				$xml .= "</consumodecombustibles:ConceptoConsumoDeCombustibles>";
			}
		}
		$xml .= "</consumodecombustibles:Conceptos>";
	}
	$xml .= "</consumodecombustibles:ConsumoDeCombustibles>";
    return $xml;
}
