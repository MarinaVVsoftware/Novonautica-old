<?php

function mf_complemento_comercioexterior11($datos)
{
    // Variable para los namespaces xml
    global $__mf_namespaces__;
    $__mf_namespaces__['cce11']['uri'] = 'http://www.sat.gob.mx/ComercioExterior11';
    $__mf_namespaces__['cce11']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/ComercioExterior11/ComercioExterior11.xsd';

    $atrs = mf_atributos_nodo($datos);
    $xml = "<cce11:ComercioExterior Version='1.1' $atrs>";
	
	if(isset($datos['Emisor']))
    {
        $atrsentidad = mf_atributos_nodo($datos['Emisor']);
        $xml .= "<cce11:Emisor $atrsentidad>";
        if(isset($datos['Emisor']['Domicilio']))
		{
			$atrs = mf_atributos_nodo($datos['Emisor']['Domicilio']);
			$xml .= "<cce11:Domicilio $atrs/>";
		}
        $xml .= "</cce11:Emisor>";
    }
	if(isset($datos['Propietario']))
    {
		foreach($datos['Propietario'] as $idx =>$entidad)
		{
			if(is_array($datos['Propietario'][$idx]))
			{
				$atrsentidad = mf_atributos_nodo($datos['Propietario'][$idx]);
				$xml .= "<cce11:Propietario $atrsentidad/>";
			}
		}
    }
    if(isset($datos['Receptor']))
    {
		$atrsentidad = mf_atributos_nodo($datos['Receptor']);
		$xml .= "<cce11:Receptor $atrsentidad>";
		if(isset($datos['Receptor']['Domicilio']))
		{
			$atrs = mf_atributos_nodo($datos['Receptor']['Domicilio']);
			$xml .= "<cce11:Domicilio $atrs/>";
		}
		$xml .= "</cce11:Receptor>";
    }
	if(isset($datos['Destinatario']))
    {
		foreach($datos['Destinatario'] as $idx =>$entidad)
		{
			if(is_array($datos['Destinatario'][$idx]))
			{
				$atrsentidad = mf_atributos_nodo($datos['Destinatario'][$idx]);
				$xml .= "<cce11:Destinatario $atrsentidad>";
				if(isset($datos['Destinatario'][$idx]['Domicilio']))
				{
					$atrs = mf_atributos_nodo($datos['Destinatario'][$idx]['Domicilio']);
					$xml .= "<cce11:Domicilio $atrs/>";
				}
				$xml .= "</cce11:Destinatario>";
			}
		}
    }
	if(isset($datos['Mercancias']))
    {
		$atrsentidad = mf_atributos_nodo($datos['Mercancias']);
		$xml .= "<cce11:Mercancias $atrsentidad>";
		foreach($datos['Mercancias'] as $idx =>$entidad)
		{
			if(is_array($datos['Mercancias'][$idx]))
			{
				$atrs = mf_atributos_nodo($datos['Mercancias'][$idx]);
				$xml .= "<cce11:Mercancia $atrs >";
				
				if(isset($datos['Mercancias'][$idx]['DescripcionesEspecificas']))
				{
					foreach($datos['Mercancias'][$idx]['DescripcionesEspecificas']  as $idx2 => $entidad2)
					{
						$atrs = mf_atributos_nodo($entidad2);
						$xml .= "<cce11:DescripcionesEspecificas $atrs/>";
					}
				}
				$xml .= "</cce11:Mercancia>";
			}	
		}
		$xml .= "</cce11:Mercancias>";
    }
	
    $xml .= "</cce11:ComercioExterior>";
    return $xml;
}
