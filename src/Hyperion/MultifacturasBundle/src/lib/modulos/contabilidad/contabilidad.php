<?php

function ___contabilidad($datos)
{
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
        if (!isset($datos['SDK']['ruta'])) {
            $datos['SDK']['ruta'] = '';
        }
        $ruta = $datos['SDK']['ruta'];
        $ruta = str_replace('\\', '/', $ruta);
        $cer = $datos['conf']['cer'];

        $certificado = cfd_certificado_pub($cer);


        if ($datos['PAC']['produccion'] != 'SI') {
            $datos['PAC']['produccion'] = 'NO';

        }
        $produccion = $datos['PAC']['produccion'];
        $rfc = $datos['PAC']['usuario'];

//echo $cer;
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

        $tipo = strtolower($datos['tipo']);
        $xml_iso8859 = '';
        switch ($tipo) {
//// CATALOGO ////////
            case "catalogo" :
                $clave = 'CT';
                if (array_key_exists('factura', $datos)) {
                    if (array_key_exists('Catalago', $datos['factura'])) {
                        $RFC = $datos['factura']['Catalago']['RFC'];
                        $mes = $datos['factura']['Catalago']['Mes'];
                        $anio = $datos['factura']['Catalago']['Anio'];

                        $xml_iso8859 .= "<catalogocuentas:Catalogo Anio=\"$anio\"
                          Certificado=\"$certificado\"
                          Mes=\"$mes\"
                          RFC=\"$anio\"
                          {SELLO}
                          Version=\"1.1\"
                          noCertificado=\"$numero_cer\"
                          xmlns:catalogocuentas=\"www.sat.gob.mx/esquemas/ContabilidadE/1_1/CatalogoCuentas\"
                          xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
                          xsi:schemaLocation=\"www.sat.gob.mx/esquemas/ContabilidadE/1_1/CatalogoCuentas http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/CatalogoCuentas/CatalogoCuentas_1_1.xsd\">";

                        if (array_key_exists('Ctas', $datos['factura']['Catalago'])) {
                            foreach ($datos['factura']['Catalago']['Ctas'] as $iidx => $tmpp) {
                                $codAgrup = $tmpp['CodAgrup'];
                                $numCta = $tmpp['NumCta'];
                                $desc = $tmpp['Desc'];
                                $nivel = $tmpp['Nivel'];
                                $natur = $tmpp['Natur'];
                                $subCtaDe = array_key_exists('SubCtaDe', $tmpp) ? "\r\n                          " . $tmpp['SubCtaDe'] . "\"" : '';

                                $xml_iso8859 .= "\r\n    <catalogocuentas:Ctas CodAgrup=\"101.01\"
                          Desc=\"Caja\"
                          Natur=\"D\"
                          Nivel=\"1\"
                          NumCta=\"1000\" $subCtaDe />";
                            }
                        }

                        // Se cierra el catalago
                        $xml_iso8859 .= "\r\n</catalogocuentas:Catalogo>";
                    }
                }
                $xslt = $__mf_constantes__['__MF_XSD_CONTA_DIR__'] . "CatalogoCuentas_1_1.xslt";

                break;
//// BALANZA ////////
            case "balanza": {
                $clave = 'BN';
                if (array_key_exists('factura', $datos)) {
                    if (array_key_exists('Balanza', $datos['factura'])) {
                        $RFC = $datos['factura']['Balanza']['RFC'];
                        $mes = $datos['factura']['Balanza']['Mes'];
                        $anio = $datos['factura']['Balanza']['Anio'];
                        $tipoEnvio = $datos['factura']['Balanza']['TipoEnvio'];
                        $fechaModBal = array_key_exists('FechaModBal', $datos['factura']['Balanza']) ? $tmp['FechaModBal'] : '';

                        $xml_iso8859 .= "<BCE:Balanza
             Anio=\"$anio\"
             Certificado=\"$certificado\"
             Mes=\"$mes\"
             RFC=\"$RFC\"
             {SELLO}
             TipoEnvio=\"N\"
             Version=\"1.1\"
             noCertificado=\"$numero_cer\"
             xmlns:BCE=\"www.sat.gob.mx/esquemas/ContabilidadE/1_1/BalanzaComprobacion\"
             xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
             xsi:schemaLocation=\"www.sat.gob.mx/esquemas/ContabilidadE/1_1/BalanzaComprobacion http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/BalanzaComprobacion/BalanzaComprobacion_1_1.xsd\">";

                        if (array_key_exists('Ctas', $datos['factura']['Balanza'])) {
                            foreach ($datos['factura']['Balanza']['Ctas'] as $iidx => $tmpp) {
                                $numCta = $tmpp['NumCta'];
                                $saldoIni = $tmpp['SaldoIni'];
                                $debe = $tmpp['Debe'];
                                $haber = $tmpp['Haber'];
                                $saldoFin = $tmpp['SaldoFin'];

                                $xml_iso8859 .= "\r\n    <BCE:Ctas Debe=\"$debe\"
                 Haber=\"$haber\"
                 NumCta=\"$numCta\"
                 SaldoFin=\"$saldoFin\"
                 SaldoIni=\"$saldoIni\" />";
                            }
                        }

                        // Se cierra la balanza
                        $xml_iso8859 .= "\r\n</BCE:Balanza>";
                    }
                }
                $xslt = $__mf_constantes__['__MF_XSD_CONTA_DIR__'] . "BalanzaComprobacion_1_1.xslt";
            }

                break;
//// POLIZA ////////
            case "poliza" :
                $clave = 'PL';
                $TipoSolicitud = $datos['factura']['Polizas']['TipoSolicitud'];
                $Anio = $datos['factura']['Polizas']['Anio'];
                $Mes = $datos['factura']['Polizas']['Mes'];
                $RFC = $datos['factura']['Polizas']['RFC'];

                $NumOrden = array_key_exists('NumOrden', $datos['factura']['Polizas']) ? "NumOrden=\"" . $datos['factura']['Polizas']['NumOrden'] . "\"" : '';
                $numTramite = array_key_exists('NumTramite', $datos['factura']['Polizas']) ? "NumTramite=\"" . $datos['factura']['Polizas']['NumTramite'] . "\"" : '';


                $xml_iso8859 = "<PLZ:Polizas Anio=\"$Anio\"
              Certificado=\"$certificado\"
              Mes=\"$Mes\"
              RFC=\"$RFC\"
              {SELLO}
              Version=\"1.1\"
             $NumOrden $numTramite             
             TipoSolicitud=\"$TipoSolicitud\"
              noCertificado=\"$numero_cer\"
             xmlns:PLZ=\"www.sat.gob.mx/esquemas/ContabilidadE/1_1/PolizasPeriodo\"
             xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
             xsi:schemaLocation=\"www.sat.gob.mx/esquemas/ContabilidadE/1_1/PolizasPeriodo http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/PolizasPeriodo/PolizasPeriodo_1_1.xsd\">";

                foreach ($datos['factura']['Polizas']['Poliza'] AS $idx => $tmp) {
                    $numUnIdenPol = $tmp['NumUnIdenPol'];
                    $fecha = $tmp['Fecha'];
                    $concepto = $tmp['Concepto'];

                    $xml_iso8859 .= "\r\n<PLZ:Poliza Concepto=\"$concepto\"
                Fecha=\"$fecha\"
                NumUnIdenPol=\"$numUnIdenPol\">\r\n";

                    foreach ($tmp['Transaccion'] as $iidx => $tmpp) {
                        $numCta = $tmpp['NumCta'];
                        $desCta = $tmpp['DesCta'];
                        $concepto = $tmpp['Concepto'];
                        $debe = $tmpp['Debe'];
                        $haber = $tmpp['Haber'];

                        $xml_iso8859 .= "\r\n<PLZ:Transaccion Concepto=\"$concepto\"
                         Debe=\"$debe\"
                         DesCta=\"$desCta\"
                         Haber=\"$haber\"
                         NumCta=\"$numCta\">";
                        if (array_key_exists('CompNal', $tmpp))
                            foreach ($tmpp['CompNal'] AS $iiidx => $tmppp) {
                                $uuid_cfdi = $tmppp['UUID_CFDI'];
                                $RFC = $tmppp['RFC'];
                                $monto = $tmppp['MontoTotal'];
                                $moneda = array_key_exists('Moneda', $tmppp) ? "Moneda=\"" . $tmppp['Moneda'] . "\"" : '';
                                $tipCamb = array_key_exists('TipCamb', $tmppp) ? "TipCamb=\"" . $tmppp['TipCamb'] . "\"" : '';

                                $xml_iso8859 .= "\r\n<PLZ:CompNal
                        UUID_CFDI=\"$uuid_cfdi\"
                        RFC=\"$RFC\"
                        MontoTotal=\"$monto\" $moneda $tipCamb />";
                            }
                        if (array_key_exists('CompNalOtr', $tmpp))
                            foreach ($tmpp['CompNalOtr'] AS $iiidx => $tmppp) {
                                $cfd_cbb_serie = array_key_exists('CFD_CBB_Serie', $tmppp) ? "CFD_CBB_Serie=\"" . $tmppp['CFD_CBB_Serie'] . "\"" : '';
                                $cfd_cbb_numfol = $tmppp['CFD_CBB_NumFol'];
                                $RFC = $tmppp['RFC'];
                                $monto = $tmppp['MontoTotal'];
                                $moneda = array_key_exists('Moneda', $tmppp) ? "Moneda=\"" . $tmppp['Moneda'] . "\"" : '';
                                $tipCamb = array_key_exists('TipCamb', $tmppp) ? "TipCamb=\"" . $tmppp['TipCamb'] . "\"" : '';

                                $xml_iso8859 .= "\r\n<PLZ:CompNalOtr $cfd_cbb_serie
                        CFD_CBB_NumFol=\"$cfd_cbb_numfol\"
                        RFC=\"$RFC\"
                        MontoTotal=\"$monto\" $moneda $tipCamb />";
                            }
                        if (array_key_exists('CompExt', $tmpp))
                            foreach ($tmpp['CompExt'] AS $iiidx => $tmppp) {
                                $numFactExt = $tmppp['NumFactExt'];
                                $taxID = array_key_exists('TaxID', $tmppp) ? "TaxID=\"" . $tmppp['TaxID'] . "\"" : '';
                                $RFC = $tmppp['RFC'];
                                $monto = $tmppp['MontoTotal'];
                                $moneda = array_key_exists('Moneda', $tmppp) ? "Moneda=\"" . $tmppp['Moneda'] . "\"" : '';
                                $tipCamb = array_key_exists('TipCamb', $tmppp) ? "TipCamb=\"" . $tmppp['TipCamb'] . "\"" : '';

                                $xml_iso8859 .= "\r\n<PLZ:CompExt
                        NumFactExt=\"$numFactExt\" $taxID
                        RFC=\"$RFC\"
                        MontoTotal=\"$monto\" $moneda $tipCamb />";
                            }
                        if (array_key_exists('Cheque', $tmpp))
                            foreach ($tmpp['Cheque'] AS $iiidx => $tmppp) {
                                $num = $tmppp['Num'];
                                $BanEmisNal = $tmppp['BanEmisNal'];
                                $BanEmisExt = array_key_exists('BanEmisExt', $tmppp) ? "BanEmisExt=\"" . $tmppp['BanEmisExt'] . "\"" : '';
                                $CtaOri = $tmppp['CtaOri'];
                                $fecha = $tmppp['Fecha'];
                                $Benef = $tmppp['Benef'];
                                $RFC = $tmppp['RFC'];
                                $monto = $tmppp['Monto'];
                                $moneda = array_key_exists('Moneda', $tmppp) ? "Moneda=\"" . $tmppp['Moneda'] . "\"" : '';
                                $tipCamb = array_key_exists('TipCamb', $tmppp) ? "TipCamb=\"" . $tmppp['TipCamb'] . "\"" : '';

                                $xml_iso8859 .= "\r\n<PLZ:Cheque 
                        Num=\"$num\" 
                        BanEmisNal=\"$BanEmisNal\" $BanEmisExt
                        CtaOri=\"$CtaOri\" 
                        Fecha=\"$fecha\" 
                        Benef=\"$Benef\" 
                        RFC=\"$RFC\" 
                        Monto=\"$monto\" $moneda $tipCamb />";
                            }
                        if (array_key_exists('Transferencia', $tmpp))
                            foreach ($tmpp['Transferencia'] AS $iiidx => $tmppp) {
                                $ctaOri = array_key_exists('CtaOri', $tmppp) ? "CtaOri=\"" . $tmppp['CtaOri'] . "\"" : '';
                                $bancoOriNal = $tmppp['BancoOriNal'];
                                $bancoOriExt = array_key_exists('BancoOriExt', $tmppp) ? "BancoOriExt=\"" . $tmppp['BancoOriExt'] . "\"" : '';
                                $ctaDest = $tmppp['CtaDest'];
                                $bancoDestNal = $tmppp['BancoDestNal'];
                                $bancoDestExt = array_key_exists('BancoDestExt', $tmppp) ? "BancoDestExt=\"" . $tmppp['BancoDestExt'] . "\"" : '';
                                $fecha = $tmppp['Fecha'];
                                $benef = $tmppp['Benef'];
                                $RFC = $tmppp['RFC'];
                                $monto = $tmppp['Monto'];
                                $moneda = array_key_exists('Moneda', $tmppp) ? "Moneda=\"" . $tmppp['Moneda'] . "\"" : '';
                                $tipCamb = array_key_exists('TipCamb', $tmppp) ? "TipCamb=\"" . $tmppp['TipCamb'] . "\"" : '';

                                $xml_iso8859 .= "\r\n<PLZ:Transferencia $ctaOri 
			BancoOriNal=\"$bancoOriNal\" $bancoOriExt
			CtaDest=\"$ctaDest\" 
			BancoDestNal=\"$bancoDestNal\" $bancoDestExt 
			Fecha=\"$fecha\" 
			Benef=\"$benef\" 
			RFC=\"$RFC\" 
			Monto=\"$monto\" $moneda $tipCamb />";
                            }
                        if (array_key_exists('OtrMetodoPago', $tmpp))
                            foreach ($tmpp['OtrMetodoPago'] AS $iiidx => $tmppp) {
                                $metPagoPol = $tmppp['MetPagoPol'];
                                $fecha = $tmppp['Fecha'];
                                $benef = $tmppp['Benef'];
                                $RFC = $tmppp['RFC'];
                                $monto = $tmppp['Monto'];
                                $moneda = array_key_exists('Moneda', $tmppp) ? "Moneda=\"" . $tmppp['Moneda'] . "\"" : '';
                                $tipCamb = array_key_exists('TipCamb', $tmppp) ? "TipCamb=\"" . $tmppp['TipCamb'] . "\"" : '';

                                $xml_iso8859 .= "		  <PLZ:OtrMetodoPago 
			MetPagoPol=\"$metPagoPol\" 
			Fecha=\"$fecha\" 
			Benef=\"$benef\" 
			RFC=\"$RFC\" 
			Monto=\"$monto\" 
			Moneda=\"$moneda\" 
			TipCamb=\"$tipCamb\" />";
                            }

                        // Se cierra la transaccion
                        $xml_iso8859 .= "        </PLZ:Transaccion>";
                    }
                    // Se cierra la poliza
                    $xml_iso8859 .= "    </PLZ:Poliza>";
                }
                // Se cierran las Polizas
                $xml_iso8859 .= "</PLZ:Polizas>";

                $xslt = $__mf_constantes__['__MF_XSD_CONTA_DIR__'] . "PolizasPeriodo_1_1.xslt";
                break;

        }

        $xml_iso8859 = contabilidad_sello($datos, $xml_iso8859, $xslt);
        if(is_array($xml_iso8859))
        {
            return $xml_iso8859;
        }

        $archivo = $datos['archivo'];

        file_put_contents($archivo, utf8_encode($xml_iso8859));

        // Se crea el archivo ZIP
        $zip = new ZipArchive();

        $archivoXML = $archivo;
        $rutaNombre = explode('/', $archivo);
        $archivoZip = array_slice($rutaNombre, 0, count($rutaNombre) - 1);
        $archivoZip = implode('/', $archivoZip);

        $archivoZip = $archivoZip . sprintf('/%s%s%s%s.zip', $datos['PAC']['usuario'], $datos['CC']['Ejercicio'], $datos['CC']['Periodo'], $clave);

        if ($zip->open($archivoZip, ZipArchive::CREATE) !== TRUE) {
            exit("cannot open <$archivoZip>\n");
        }

        // Se agrega el archivo
        $zip->addFile($archivoXML, $rutaNombre[count($rutaNombre) - 1]);
        $zip->close();
        return array($archivo, $archivoZip);
    }

}
