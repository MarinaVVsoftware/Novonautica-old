<?php

function mf_init_nodo_cfdi(array &$datos = array())
{
    // Globales a usar
    global $__mf_constantes__;
    global $__mf_namespaces__;

    // Se indican los namespaces para CFDi 3.3
    $__mf_namespaces__['cfdi']['uri'] = 'http://www.sat.gob.mx/cfd/3';
    $__mf_constantes__['__MF_VERSION_CFDI__'] = '3.3';

    if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.3')
    {
        $__mf_namespaces__['cfdi']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd';
    }
    else
    {
        $__mf_namespaces__['cfdi']['xsd'] = 'http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd';
    }

	if(isset($datos['emisor']['rfc']))
	{
		$datos['emisor']['rfc'] = cfd_formato_rfc($datos['emisor']['rfc']);
	}
	if(isset($datos['emisor']['Rfc']))
	{
		$datos['emisor']['Rfc'] = cfd_formato_rfc($datos['emisor']['Rfc']);
	}
	if(isset($datos['receptor']['rfc']))
	{
		$datos['receptor']['rfc'] = cfd_formato_rfc($datos['receptor']['rfc']);
	}
	if(isset($datos['receptor']['Rfc']))
	{
		$datos['receptor']['Rfc'] = cfd_formato_rfc($datos['receptor']['Rfc']);
	}

/*
    // Se agregan los alias
    mf_carga_utileria('alias');

    // Se ajusta la estructura
    $datos = mf_modulos_pre('ajusta_estructura', $datos);
 */
    // Version de CFDi
    selecciona_version_cfdi($datos);

    // Ruta de la carpeta complementos
    $__mf_constantes__['__MF_COMPLEMENTOS_DIR__'] = $__mf_constantes__['__MF_NODOS_DIR__'] . $__mf_constantes__['__MF_TIPO_DOCUMENTO__'] . '/complementos/';
    // Ruta XSD de Contabilidad
    $__mf_constantes__['__MF_XSD_CONTA_DIR__'] = $__mf_constantes__['__MF_NODOS_DIR__'] . $__mf_constantes__['__MF_TIPO_DOCUMENTO__'] . '/sat/contabilidad/';
    // Ruta XSD de Contabilidad
    $__mf_constantes__['__MF_XSD_CONTA13_DIR__'] = $__mf_constantes__['__MF_NODOS_DIR__'] . $__mf_constantes__['__MF_TIPO_DOCUMENTO__'] . '/sat/contabilidad13/';

    // Ruta de XSD
    $__mf_constantes__['__MF_XSD_DIR__'] = $__mf_constantes__['__MF_NODOS_DIR__'] . $__mf_constantes__['__MF_TIPO_DOCUMENTO__'] . '/sat/xsd' . ($__mf_constantes__['__MF_VERSION_CFDI__'] * 10)  . '/';
    // Raiz de XSLT
    $__mf_constantes__['__MF_XSLT_DIR__'] = $__mf_constantes__['__MF_NODOS_DIR__'] . $__mf_constantes__['__MF_TIPO_DOCUMENTO__'] . '/sat/xslt' . ($__mf_constantes__['__MF_VERSION_CFDI__'] * 10) . '/';

    // Se preparan los certificados (DEBE ESTAR AL INICIO PARA QUE AGREGUE EL NUMERO DEL CERTIFICADO)
    $rr = mf_prepara_certificados($datos);
    if($rr['abortar'] == true OR (isset($rr['codigo_mf_numero']) &&  $rr['codigo_mf_numero'] == 7))
    {
        mf_reset_variables();
        return $rr['respuesta'];
    }

    // Se cargan los alias
    mf_carga_utileria('alias');

    global $__mf_constantes__;

    // Se verifica la version
    switch ($__mf_constantes__['__MF_VERSION_CFDI__'])
    {
        case '3.2':
            // Complemento Nomina
            if($datos['modonomina'] == 'SI')
            {
                $datos['complemento'] = 'nomina12';

                $nomina = $datos['nomina'];

                $datos['nomina12'] = $nomina['datos'];
                unset($nomina['datos']);
                unset($datos['nomina']);
                $datos['nomina12'] = array_merge($datos['nomina12'], $nomina);

                if(isset($datos['nomina12']['emisor']))
                {
                    $datos['nomina12']['Emisor'] = $datos['nomina12']['emisor'];
                    unset($datos['nomina12']['emisor']);
                }

                if(isset($datos['nomina12']['receptor']))
                {
                    $datos['nomina12']['Receptor'] = $datos['nomina12']['receptor'];
                    unset($datos['nomina12']['receptor']);
                }

                if(isset($datos['nomina12']['percepciones']))
                {
                    $datos['nomina12']['Percepciones'] = $datos['nomina12']['percepciones'];
                    unset($datos['nomina12']['percepciones']);
                }

                if(isset($datos['nomina12']['deducciones']))
                {
                    $datos['nomina12']['Deducciones'] = $datos['nomina12']['deducciones'];
                    unset($datos['nomina12']['deducciones']);
                }

                if(isset($datos['nomina12']['otrospagos']))
                {
                    $datos['nomina12']['OtrosPagos'] = $datos['nomina12']['otrospagos'];
                    unset($datos['nomina12']['otrospagos']);
                }

                if(isset($datos['nomina12']['deducciones']))
                {
                    $datos['nomina12']['Incapacidades'] = $datos['nomina12']['deducciones'];
                    unset($datos['nomina12']['deducciones']);
                }
            }
            break;
        case '3.3':
            break;
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function mf_nodo_cfdi(array &$datos)
{
    global $__mf_constantes__;
    $respuesta_sdk = array();

    // Se agregan los alias
    mf_carga_utileria('alias');
    // Se ajusta la estructura
    $datos = mf_modulos_pre('ajusta_estructura', $datos);

    $xml_a_timbrar = '';

	if(isset($datos['xml']))
	{
		$xml_a_timbrar = file_get_contents($datos['xml']);
		$atr_sello = mf_busca_alias('factura.sello');
		$atrs_cfdi = mf_atributos_nodo($datos['factura'], 'factura') . "$atr_sello='{SELLO}'";
		$xml_a_timbrar = str_replace('{SELLO}', $atrs_cfdi, $xml_a_timbrar);
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
				$complemento_xml = '';
				if(isset($datos['complemento']))
				{
					$complemento_xml = '<cfdi:Complemento>';

					$complementos = explode(',', $datos['complemento']);

					foreach($complementos as $idx => $complemento)
					{
						$complemento = trim($complemento);
						if(!empty($complemento))
						{
							if(isset($datos[$complemento]))
							{
								$complemento_xml .= mf_carga_complemento($complemento, $datos[$complemento]);
							}
						}
					}
					$complemento_xml .= '</cfdi:Complemento>';
				}

				// Se sella el XML
				$sello = mf_busca_alias('factura.sello');
				$atr = mf_agrega_namespaces() . mf_atributos_nodo($datos['factura'], 'factura') . "$sello='{SELLO}'";

				$comprobante = "<cfdi:Comprobante $atr>$cfdisrelacionados$emisor$receptor$conceptos$impuestos$complemento_xml</cfdi:Comprobante>";
				$xml_a_timbrar = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n$comprobante";
				break;
			}
		}
	}

    $xml_cfdi = $xml_a_timbrar;

    // Ajuste para 3.2
    if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.2')
    {
        $xml_cfdi = utf8_encode($xml_cfdi);
    }

    // Se agrega el XML al debug trace
    global $__mf_xml_template__;
    $__mf_xml_template__ = $xml_cfdi;

    // Se el XML
    mf_agrega_global('xml_cfdi', $xml_cfdi);

    // Se guarda la ruta del temporal
    $xmltmp = $__mf_constantes__['__MF_SDK_TMP__'] . md5(time() . rand(1111, 9999)) . '.xml';
    mf_agrega_global('ruta_tmp', $xmltmp);

    // Se crea el archivo XML
    $tmpok = file_put_contents(mf_recupera_global('ruta_tmp'), mf_recupera_global('xml_cfdi'));

    // Valores por defecto para la respuesta
    $codigo_mf_numero = -1;
    $codigo_mf_texto = 'ERROR';
    $timbrado = '';
    $uuid = '';
    $cadena_sat = '';
    $timbre_version = '';
    $timbre_fecha = '';
    $timbre_selloCFD = '';
    $timbre_selloSAT = '';
    $timbre_noCertificadoSAT = '';

///aki $tmpok  no entra
    if($tmpok == false)
    {
        $codigo_mf_numero = 7;
        $codigo_mf_texto = 'ERROR AL ESCRIBIR EN EL DIRECTORIO TEMPORAL';
    }
    else
    {
        // Se sella el XML
        $cadenaOrig = mf_sellar_xml(mf_recupera_global('ruta_tmp'), $datos['conf']['key'], $datos);

        if (is_array($cadenaOrig) && $cadenaOrig['abortar'] == true)
        {
            $respuesta_sdk['codigo_mf_numero'] = $cadenaOrig['respuesta']['codigo_mf_numero'];
            $respuesta_sdk['codigo_mf_texto'] = $cadenaOrig['respuesta']['codigo_mf_texto'];

            // Se ejecutan modulos inter
            if (isset($datos['modulos_inter'])) {
                // Datos de debug
                global $__mf_debug_trace__;

                // Se verifica que se pueda serializar el xml
                $xml_serializado = json_encode($__mf_xml_template__);
                if($xml_serializado == 'null')
                {
                    $__mf_xml_template__ = base64_encode($__mf_xml_template__);
                }

                $__mf_debug_trace__ = array(
                    'entrada' => $datos,
                    'salida' => $respuesta_sdk,
                    'xml_template' => $__mf_xml_template__
                );

                // Se obtienen los nombres de los modulos
                $modulos_inter = explode(',', $datos['modulos_inter']);

                // Se ejecutan los modulos
                foreach ($modulos_inter as $mod) {
                    mf_modulo_inter(trim($mod), $datos);
                    $respuesta_sdk['respuesta_modulos'] = json_encode($__mf_respuesta_modulos__);
                }
            }

            // Se borra el temporal
            unlink($xmltmp);
        }
        else
        {
            // Se guarda la cadena original
            mf_agrega_global('cadena_original', $cadenaOrig);
            global $__mf_debug_trace__;
            $__mf_debug_trace__['cadena_original'] = mf_recupera_global('cadena_original');

            // Se genera XML_DEBUG
            // Se valida que esten definias las variables
            $__mf_rfc_pruebas__ = 'LAN7008173R5';

            if (isset($datos['xml_debug']) &&
                isset($datos['emisor']['rfc']) &&
                isset($datos['PAC']['usuario']) &&
                isset($datos['PAC']['pass']) &&
                // Se revisa que sea el rfc de pruebas
                strtoupper(trim($datos['emisor']['rfc'])) == $__mf_rfc_pruebas__ &&
                // Se revisan credenciales de prueba
                $datos['PAC']['usuario'] == 'DEMO700101XXX' &&
                $datos['PAC']['pass'] == 'DEMO700101XXX'
            ) {
                $xml_debug = mf_recupera_global('xml_cfdi');
                if (preg_match('!\S!u', $xml_debug)) {
                } else {
                    $xml_debug = utf8_encode($xml_debug);
                }
                // Se escribe el xml_debug
                if($__mf_constantes__['__MF_PRODUCCION__'] == 'NO')
                {
                    if($xml_debug!='')
                    {
                        file_put_contents($datos['xml_debug'], $xml_debug);
                    }
                }
            }

            // Se valida con el xsd
            $xsdfile = $__mf_constantes__['__MF_XSD_DIR__'] . 'cfdv33.xsd';

            // Para omitir la validacion
            if(isset($datos['validacion_local']) && $datos['validacion_local']=='NO')
            {
                $omitir_xsd='SI';
            }
            else
            {
                $omitir_xsd='NO';
            }

            $res_valida = mf_valida_cfdi33(mf_recupera_global('ruta_tmp'), $xsdfile, $omitir_xsd);
            if ($res_valida['abortar'] == true) {
                // Se borra el temporal
                if (file_exists($xmltmp)) {
                    unlink($xmltmp);
                }
                // Se devuelve la respuesa
                $respuesta_sdk['codigo_mf_numero'] = $res_valida['respuesta']['codigo_mf_numero'];
                $respuesta_sdk['codigo_mf_texto'] = $res_valida['respuesta']['codigo_mf_texto'];

                // Se ejecutan modulos inter
                if (isset($datos['modulos_inter'])) {
                    // Datos de debug
                    global $__mf_debug_trace__;
                    $__mf_debug_trace__ = array(
                        'entrada' => $datos,
                        'salida' => $respuesta_sdk,
                        'xml_template' => $__mf_xml_template__
                    );

                    // Se obtienen los nombres de los modulos
                    $modulos_inter = explode(',', $datos['modulos_inter']);

                    // Se ejecutan los modulos
                    foreach ($modulos_inter as $mod) {
                        mf_modulo_inter(trim($mod), $datos);
                        $respuesta_sdk['respuesta_modulos'] = json_encode($__mf_respuesta_modulos__);
                    }
                }
            }
            else
            {

                // Se ejecutan modulos inter
                if(isset($datos['modulos_inter']))
                {
                    // Datos de debug
                    global $__mf_debug_trace__;
                    $__mf_debug_trace__ = array(
                        'entrada' => $datos,
                        'salida' => $respuesta_sdk,
                        'xml_template' => $__mf_xml_template__
                    );

                    // Se obtienen los nombres de los modulos
                    $modulos_inter = explode(',', $datos['modulos_inter']);

                    // Se ejecutan los modulos
                    foreach($modulos_inter as $mod)
                    {
                        $res_mod = mf_modulo_inter(trim($mod), $datos);
                        if($res_mod['abortar'] == true)
                        {
                            mf_reset_variables();
                            return $res_mod['respuesta'];
                        }
                        //$respuesta_sdk['respuesta_modulos'] = json_encode($__mf_respuesta_modulos__);
                    }
                }

                // Se timbra el XML
                $res = mf_timbrar_cfdi(rand(1, 10), $datos['PAC']['usuario'], $datos['PAC']['pass'], mf_recupera_global('ruta_tmp'), $datos['retencion']);
                $respuesta_sdk = array_merge($respuesta_sdk, $res);

                /*if (isset($res['abortar']) && $res['abortar'] == true)
                {
                    // Se borra el temporal
                    unlink($xmltmp);

                    //$respuesta_sdk['codigo_mf_numero'] = $res['respuesta']['codigo_mf_numero'];
                    //$respuesta_sdk['codigo_mf_texto'] = $res['respuesta']['codigo_mf_texto'];
                    $respuesta_sdk = array_merge($respuesta_sdk, $res);

                    // Se elimina el campo abortar
                    unset($res['abortar']);

                    // Se cargan modulos inter
                    if (isset($datos['modulos_inter'])) {
                        // Datos de debug
                        global $__mf_debug_trace__;
                        $__mf_debug_trace__ = array(
                            'entrada' => $datos,
                            'salida' => $respuesta_sdk,
                            'xml_template' => $__mf_xml_template__
                        );

                        $modulos_inter = explode(',', $datos['modulos_inter']);
                        foreach ($modulos_inter as $mod) {
                            mf_modulo_inter(trim($mod), $datos);
                            $respuesta_sdk['respuesta_modulos'] = json_encode($__mf_respuesta_modulos__);
                        }
                    }
                    mf_reset_variables();
                }
                else*/
                {
                    unset($res['abortar']);

                    // Se valida si se timbro la factura
                    if ($res['codigo_mf_numero'] != 0)
                    {
                        // Se borra el temporal
                        unlink($xmltmp);

                        $ultimo_xml_error = file_get_contents(mf_recupera_global('ruta_tmp'));
                        if (preg_match('!\S!u', $ultimo_xml_error)) {

                        } else {
                            $ultimo_xml_error = utf8_encode($ultimo_xml_error);
                        }

                        file_put_contents($__mf_constantes__['__MF_SDK_TMP__'] . 'ultimo_error.xml', $ultimo_error);

                        return $respuesta_sdk;
                    } else {
                        // Se lee el timbre
                        $respuesta_sdk['cfdi'] = $res['cfdi'];
                        $xml = simplexml_load_string($res['cfdi']);
                        $ns = $xml->getNamespaces(true);
                        $xml->registerXPathNamespace('t', $ns['tfd']);

                        // Campos para generar las cadenas de la respuesta
                        $campos['3.3']['SelloCFD'] = 'SelloCFD';
                        $campos['3.2']['SelloCFD'] = 'selloCFD';
                        $campos['3.3']['NoCertificadoSAT'] = 'NoCertificadoSAT';
                        $campos['3.2']['NoCertificadoSAT'] = 'noCertificadoSAT';
                        $campos['3.3']['FechaTimbrado'] = 'FechaTimbrado';
                        $campos['3.2']['FechaTimbrado'] = 'FechaTimbrado';
                        $campos['3.3']['Version'] = 'Version';
                        $campos['3.2']['Version'] = 'version';
                        $campos['3.3']['SelloSAT'] = 'SelloSAT';
                        $campos['3.2']['SelloSAT'] = 'selloSAT';

                        foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
                            $uuid = $tfd['UUID'];

                            $timbre_selloCFD = $tfd[$campos[$__mf_constantes__['__MF_VERSION_CFDI__']]['SelloCFD']];
                            $timbre_noCertificadoSAT = $tfd[$campos[$__mf_constantes__['__MF_VERSION_CFDI__']]['NoCertificadoSAT']];
                            $timbre_fecha = $tfd[$campos[$__mf_constantes__['__MF_VERSION_CFDI__']]['FechaTimbrado']];
                            $timbre_version = $tfd[$campos[$__mf_constantes__['__MF_VERSION_CFDI__']]['Version']];
                            $timbre_selloSAT = $sellosat = $tfd[$campos[$__mf_constantes__['__MF_VERSION_CFDI__']]['SelloSAT']];
                        }

                        $cadena_sat = "||$timbre_version|$uuid|$timbre_fecha|$timbre_selloCFD|$timbre_noCertificadoSAT||";

                        $respuesta_sdk = array_merge($respuesta_sdk, $res);
                        $respuesta_sdk['archivo_xml'] = $datos['cfdi'];
                        $respuesta_sdk['png'] = $res['png'];
                        $respuesta_sdk['archivo_png'] = substr($respuesta_sdk['archivo_xml'], 0, -3) . 'png';
                        if ($respuesta_sdk['png'] != '')
                            file_put_contents($respuesta_sdk['archivo_png'], base64_decode($respuesta_sdk['png']));
                        $respuesta_sdk['representacion_impresa_cadena'] = $cadena_sat;
                        $respuesta_sdk['representacion_impresa_certificado_no'] = $datos['factura']['noCertificado'];
                        $respuesta_sdk['representacion_impresa_fecha_timbrado'] = $timbre_fecha;
                        $respuesta_sdk['representacion_impresa_sello'] = $timbre_selloCFD;
                        $respuesta_sdk['representacion_impresa_selloSAT'] = $timbre_selloSAT;
                        $respuesta_sdk['representacion_impresa_certificadoSAT'] = $timbre_noCertificadoSAT;

                        // Se guarda el XML
                        if($__mf_constantes__['__MF_VERSION_CFDI__'] == '3.3')
                        {
                            if (preg_match('!\S!u', $res['cfdi'])) {

                            } else {
                                $res['cfdi'] = utf8_encode($res['cfdi']);
                            }
                        }
                        else
                        {
                            $res['cfdi'] = utf8_decode($res['cfdi']);
                        }

                        file_put_contents($datos['cfdi'], $res['cfdi']);
                    }

                    // Se borra el temporal
                    unlink($xmltmp);
                }
            }
        }

        // Se elimina el archivo temporal
        if (file_exists($xmltmp)) {
            unlink($xmltmp);
        }
    }

    // Se retorna el XML
    //return $xml_a_timbrar;
    return $respuesta_sdk;
}
