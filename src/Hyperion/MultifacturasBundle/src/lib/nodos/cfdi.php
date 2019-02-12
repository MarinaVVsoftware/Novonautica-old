<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////


function mf_init_nodo_cfdi(array &$datos = array())
{
    // Globales a usar
    global $__mf_constantes__;
    global $__mf_namespaces__;

    // Se indican los namespaces para CFDi 3.3
    $__mf_namespaces__['cfdi']['uri'] = 'http://www.sat.gob.mx/cfd/3';
    if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.3')
    {
        $__mf_namespaces__['cfdi']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd';
    }
    else
    {
        $__mf_namespaces__['cfdi']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd';
    }
/*
    // Se agregan los alias
    mf_carga_utileria('alias');

    // Se ajusta la estructura
    $datos = mf_modulos_pre('ajusta_estructura', $datos);
 */   
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function mf_nodo_cfdi(array &$datos)
{
//// mash
    // Se agregan los alias
    mf_carga_utileria('alias');
    // Se ajusta la estructura
    $datos = mf_modulos_pre('ajusta_estructura', $datos);
    
/////fin mash

    global $__mf_constantes__;
	
	if(isset($datos['xml']))
	{
		$xml_a_timbrar = file_get_contents($datos['xml']);
	}
	else
	{
		// Se verifica la version
		switch ($__mf_constantes__['__MF_VERSION_CFDI__'])
		{
			case '3.2':
			{
				// Emisor
				$emisor = '';
				if(isset($datos['emisor']))
				{
					$emisor .= mf_carga_nodo('emisor', $datos['emisor']);
				}

				// Receptor
				$receptor = '';
				if(isset($datos['receptor']))
				{
					$receptor .= mf_carga_nodo('receptor', $datos['receptor']);
				}

				// Conceptos
				$conceptos = '';
				if(isset($datos['conceptos']))
				{
					$conceptos .= mf_carga_nodo('conceptos', $datos);
				}

				// Impuestos
				$impuestos = '';
				if(isset($datos['impuestos']))
				{
					$impuestos .= mf_carga_nodo('impuestos', $datos['impuestos']);
				}

				// Se agrega el complemento
				$complemento = '<cfdi:Complemento>';
				
				if(isset($datos['complemento']))
				{
					if(isset($datos[$datos['complemento']]))
					{
						$complemento .= mf_carga_complemento($datos['complemento'], $datos[$datos['complemento']]);
					}
				}
				$complemento .= '</cfdi:Complemento>';

				// Se sella el XML
				$sello = mf_busca_alias('factura.sello');
				$atr = mf_agrega_namespaces() . mf_atributos_nodo($datos['factura'], 'factura') . "$sello='{SELLO}'";

				$comprobante = "<cfdi:Comprobante $atr>$emisor$receptor$conceptos$impuestos$complemento</cfdi:Comprobante>";
				$xml_a_timbrar = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n$comprobante";
				break;
			}
			case '3.3':
			{
				// CfdisRelacionados
				$cfdisrelacionados = '';
				if(isset($datos['CfdisRelacionados']))
				{
					$cfdisrelacionados .= mf_carga_nodo('cfdisrelacionados', $datos['CfdisRelacionados']);
				}

				// Emisor
				$emisor = '';
				if(isset($datos['emisor']))
				{
					$emisor .= mf_carga_nodo('emisor', $datos['emisor']);
				}

				// Receptor
				$receptor = '';
				if(isset($datos['receptor']))
				{
					$receptor .= mf_carga_nodo('receptor', $datos['receptor']);
				}

				// Conceptos
				$conceptos = '';
				if(isset($datos['conceptos']))
				{
					$conceptos .= mf_carga_nodo('conceptos', $datos);
				}

				// Impuestos
				$impuestos = '';
				if(isset($datos['impuestos']))
				{
					$impuestos .= mf_carga_nodo('impuestos', $datos['impuestos']);
				}

				// Se agrega el complemento
				$complemento = '';
				if(isset($datos['complemento']))
				{
					$complemento = '<cfdi:Complemento>';
					if(isset($datos[$datos['complemento']]))
					{
						$complemento .= mf_carga_complemento($datos['complemento'], $datos[$datos['complemento']]);
					}
					$complemento .= '</cfdi:Complemento>';
				}

				// Se sella el XML
				$sello = mf_busca_alias('factura.sello');
				$atr = mf_agrega_namespaces() . mf_atributos_nodo($datos['factura'], 'factura') . "$sello='{SELLO}'";

				$comprobante = "<cfdi:Comprobante $atr>$cfdisrelacionados$emisor$receptor$conceptos$impuestos$complemento</cfdi:Comprobante>";
				$xml_a_timbrar = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n$comprobante";
				break;
			}
		}
	}

    // Se retorna el XML
    return $xml_a_timbrar;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
function mf_crea_xml_cfdi32(array $datos)
{
    global $__mf_constantes__;
    // Se preparan los certificados (DEBE ESTAR AL INICIO PARA QUE AGREGUE EL NUMERO DEL CERTIFICADO)
    mf_prepara_certificados($datos);

    $emisor = mf_nodo_emisor($datos['emisor']);
    $receptor = mf_nodo_receptor($datos['receptor']);
    $conceptos = mf_nodo_conceptos($datos['conceptos']);
    $impuestos = mf_nodo_impuestos($datos['impuestos']);
    $sello = mf_busca_alias('factura.sello');
    $atr = mf_atributos_nodo($datos['factura'], 'factura') . "$sello='{SELLO}'" . mf_agrega_namespaces();
    $complemento = '';
    if(isset($datos['complemento']))
    {
        $complemento = mf_carga_complemento($datos['complemento'], $datos[$datos['complemento']]);
    }
    $comprobante = "<cfdi:Comprobante $atr>$emisor$receptor$conceptos$impuestos$complemento</cfdi:Comprobante>";
    $xml_a_timbrar = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n$comprobante";

    // Se guarda de forma temporal
    $xmltmp = $__mf_constantes__['__MF_SDK_TMP__'] . md5(time() . rand(1111, 9999)) . '.xml';
    file_put_contents($xmltmp, $xml_a_timbrar);

    // Se sella el XML
    mf_sellar_xml($xmltmp, $datos['conf']['key'], $datos);

    // Se timbra el XML
    $res = mf_timbrar_cfdi(rand(1, 10), $datos['PAC']['usuario'], $datos['PAC']['pass'], $xmltmp, $datos['retencion']);

    return $res;
}

*/
