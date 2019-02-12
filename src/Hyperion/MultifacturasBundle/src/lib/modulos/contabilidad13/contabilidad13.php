<?php

function ___contabilidad13(&$datos)
{
	// Se declara que se van a utilizar las variables globales
    global $__mf_constantes__;
	
    {
// DA FORMATO
        if (!isset($datos['html_a_txt'])) {
            $datos['html_a_txt'] = '';
        }
        if ($datos['html_a_txt'] == 'SI') {
            $datos = array_map_recursive('cfd_fix_dato_xml_html_txt', $datos);
        }

        if (!isset($datos['remueve_acentos'])) {
            $datos['remueve_acentos'] = '';
        }
        if ($datos['remueve_acentos'] == 'SI') {
            $datos = array_map_recursive('cfd_fix_dato_xml_acentos', $datos);
        } else {
            $datos = array_map_recursive('cfd_fix_dato_xml', $datos);
        }


//LEE VARIABLES
        $ruta = $datos['SDK']['ruta'];
        $ruta = str_replace('\\', '/', $ruta);
        $cer = $datos['conf']['cer'];

        if ($datos['PAC']['produccion'] != 'SI') {
            $datos['PAC']['produccion'] = 'NO';
        }
        $produccion = $datos['PAC']['produccion'];
        $rfc = $datos['PAC']['usuario'];

        if (file_exists("$cer.txt")) {
            $numero_cer = file_get_contents("$cer.txt");
        } else {
            $res['produccion'] = $produccion;
            $res['codigo_mf_numero'] = 7;
            $res['codigo_mf_texto'] = 'CERTIFICADO NO VALIDO, NO SE PUDO LEER EL NUMERO DEL CERTIFICADO';
            $res['cancelada'] = 1;
            $res['servidor'] = 0;
            return $res;
        }
		
		// Se preparan los certificados
		mf_prepara_certificados($datos);

		// Se valida el tipo de documento
        $tipo = strtolower($datos['tipo']);
		switch($tipo)
		{
			case 'balanza':
				$xml_iso8859 = balanza($datos);
				$xslt = $__mf_constantes__['__MF_XSD_CONTA13_DIR__'] . 'BalanzaComprobacion_1_2.xslt';
				$nomenclatura = sprintf('/%s%s%sB%s', $datos['Balanza']['RFC'], $datos['Balanza']['Anio'], $datos['Balanza']['Mes'], $datos['Balanza']['TipoEnvio']);
				break;
			case 'catalogo':
				$xml_iso8859 = catalogo($datos);
				$xslt = $__mf_constantes__['__MF_XSD_CONTA13_DIR__'] . 'CatalogoCuentas_1_2.xslt';
				$nomenclatura = sprintf('/%s%s%sCT', $datos['Catalogo']['RFC'], $datos['Catalogo']['Anio'], $datos['Catalogo']['Mes']);
				break;
			case 'poliza':
				$xml_iso8859 = poliza($datos);
				$xslt = $__mf_constantes__['__MF_XSD_CONTA13_DIR__'] . 'PolizasPeriodo_1_2.xslt';
				$nomenclatura = sprintf('/%s%s%sPL', $datos['Polizas']['RFC'], $datos['Polizas']['Anio'], $datos['Polizas']['Mes']);
				break;
			default:
				$xml_iso8859 = '';
				break;
		}

		// Se genera el XML
		$sello = mf_sellar_contabilidad13($xml_iso8859, $xslt, $datos['conf']['key']);
		
		if($sello['abortar'] != false)
		{
			echo $sello['respuesta'];
			die();
		}
		
		// Se agrega el sello
		$xml_iso8859 = str_replace('{SELLO}', $sello['respuesta'], $xml_iso8859);

		// Se obtiene la ruta final del archivo
        $archivo = $datos['ruta_archivo'] . $nomenclatura . '.xml';

		// Se genera el archivo
        file_put_contents($archivo, utf8_encode($xml_iso8859));

        // Se crea el archivo ZIP
        $zip = new ZipArchive();

        $archivoXML = $archivo;
        $rutaNombre = explode('/', $archivo);
        $archivoZip = array_slice($rutaNombre, 0, count($rutaNombre) - 1);
        $archivoZip = implode('/', $archivoZip);

        $archivoZip = $archivoZip . $nomenclatura . '.zip';

        if ($zip->open($archivoZip, ZipArchive::CREATE) !== TRUE) {
            exit("cannot open <$archivoZip>\n");
        }

        // Se agrega el archivo
        $zip->addFile($archivoXML, $rutaNombre[count($rutaNombre) - 1]);
        $zip->close();
        return array($archivo, $archivoZip);
    }
}

function poliza($datos)
{
	// Se acomodan los campos certificado y noCertificado
	$datos['Polizas']['Certificado'] = $datos['factura']['certificado'];
	$datos['Polizas']['noCertificado'] = $datos['factura']['noCertificado'];
	unset($datos['factura']);
	
	foreach($datos['Polizas']['Poliza'] as $idxp => $poliza)
	{
		$nodos_transaccion = '';
		foreach($poliza['Transaccion'] as $idxt => $transaccion)
		{
			$atrs_transaccion = mf_atributos_nodo($transaccion, '');
			
			$nodos_compnal='';
			foreach($transaccion['CompNal'] as $idxcn => $compnal)
			{
				$atrs_compnal = mf_atributos_nodo($compnal, '');
				$nodos_compnal .= "<PLZ:CompNal $atrs_compnal></PLZ:CompNal>";
			}
			
			$nodos_compnalotr='';
			foreach($transaccion['CompNalOtr'] as $idxcno => $compnalotr)
			{
				$atrs_compnalotr = mf_atributos_nodo($compnalotr, '');
				$nodos_compnalotr .= "<PLZ:CompNalOtr $atrs_compnalotr></PLZ:CompNalOtr>";
			}
			
			$nodos_compext='';
			foreach($transaccion['CompExt'] as $idxcme => $compext)
			{
				$atrs_compext = mf_atributos_nodo($compext, '');
				$nodos_compext .= "<PLZ:CompExt $atrs_compext></PLZ:CompExt>";
			}
			
			$nodos_cheque='';
			foreach($transaccion['Cheque'] as $idxcheque => $cheque)
			{
				$atrs_cheque = mf_atributos_nodo($cheque, '');
				$nodos_cheque .= "<PLZ:Cheque $atrs_cheque></PLZ:Cheque>";
			}
			
			$nodos_transferencia='';
			foreach($transaccion['Transferencia'] as $idxcheque => $transferencia)
			{
				$atrs_transferencia= mf_atributos_nodo($transferencia, '');
				$nodos_cheque .= "<PLZ:Transferencia $atrs_transferencia></PLZ:Transferencia>";
			}
			
			$nodos_otrometodopago='';
			foreach($transaccion['OtrMetodoPago'] as $idxcheque => $otrmetodopago)
			{
				$atrs_otrmetodopago= mf_atributos_nodo($otrmetodopago, '');
				$nodos_otrometodopago .= "<PLZ:OtrMetodoPago $atrs_otrmetodopago></PLZ:OtrMetodoPago>";
			}
			
			$nodos_transaccion .= "<PLZ:Transaccion $atrs_transaccion>$nodos_compnal$nodos_compnalotr$nodos_compext$nodos_cheque$nodos_transferencia$nodos_otrometodopago</PLZ:Transaccion>";
		}
		
		$atrs_poliza = mf_atributos_nodo($poliza, '');
		$nodos_poliza .= "<PLZ:Poliza $atrs_poliza>$nodos_transaccion</PLZ:Poliza>";
	}
	
	$atrs_polizas = mf_atributos_nodo($datos['Polizas'], '');
	$xml_polizas = '<?xml version="1.0" encoding="UTF-8" ?>'."\n<PLZ:Polizas xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:PLZ=\"http://www.sat.gob.mx/esquemas/ContabilidadE/1_3/PolizasPeriodo\" xsi:schemaLocation=\"http://www.sat.gob.mx/esquemas/ContabilidadE/1_3/PolizasPeriodo http://www.sat.gob.mx/esquemas/ContabilidadE/1_3/PolizasPeriodo/PolizasPeriodo_1_3.xsd\" Version='1.3' $atrs_polizas Sello='{SELLO}'>$nodos_poliza</PLZ:Polizas>";
	return $xml_polizas;
}

function catalogo($datos)
{
	// Se acomodan los campos certificado y noCertificado
	$datos['Catalogo']['Certificado'] = $datos['factura']['certificado'];
	$datos['Catalogo']['noCertificado'] = $datos['factura']['noCertificado'];
	unset($datos['factura']);
	
	// Atributos del nodo Catalogo
	$atrs_catalogo = mf_atributos_nodo($datos['Catalogo'], '');
	
	// Nodos Ctas
	$xml_ctas = '';
	foreach($datos['Catalogo']['Ctas'] as $idx => $ctas)
	{
		$atrs_cta = mf_atributos_nodo($ctas, '');
		$xml_ctas .= "<catalogocuentas:Ctas $atrs_cta />";;
	}
	
	// XML de Catalogo completo
	$xml_balanza = '<?xml version="1.0" encoding="UTF-8" ?>'."\n<catalogocuentas:Catalogo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:catalogocuentas=\"http://www.sat.gob.mx/esquemas/ContabilidadE/1_3/CatalogoCuentas\" xsi:schemaLocation=\"http://www.sat.gob.mx/esquemas/ContabilidadE/1_3/CatalogoCuentas http://www.sat.gob.mx/esquemas/ContabilidadE/1_3/CatalogoCuentas/CatalogoCuentas_1_3.xsd\" Version='1.3' $atrs_catalogo Sello='{SELLO}'>$xml_ctas</catalogocuentas:Catalogo>";
	return $xml_balanza;
}

function balanza($datos)
{
	// Se acomodan los campos certificado y noCertificado
	$datos['Balanza']['Certificado'] = $datos['factura']['certificado'];
	$datos['Balanza']['noCertificado'] = $datos['factura']['noCertificado'];
	unset($datos['factura']);
	
	// Atributos del nodo Balanza
	$atrs_balanza = mf_atributos_nodo($datos['Balanza'], '');
	
	// Nodos Ctas
	$xml_ctas = '';
	foreach($datos['Balanza']['Ctas'] as $idx => $ctas)
	{
		$atrs_cta = mf_atributos_nodo($ctas, '');
		$xml_ctas .= "<BCE:Ctas $atrs_cta />";;
	}
	
	// XML de Balanza completo
	$xml_balanza = '<?xml version="1.0" encoding="UTF-8" ?>'."\n<BCE:Balanza xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:BCE=\"http://www.sat.gob.mx/esquemas/ContabilidadE/1_3/BalanzaComprobacion\" xsi:schemaLocation=\"http://www.sat.gob.mx/esquemas/ContabilidadE/1_3/BalanzaComprobacion http://www.sat.gob.mx/esquemas/ContabilidadE/1_3/BalanzaComprobacion/BalanzaComprobacion_1_3.xsd\" Version='1.3' $atrs_balanza Sello='{SELLO}'>$xml_ctas</BCE:Balanza>";
	return $xml_balanza;
}