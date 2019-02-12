<?php
error_reporting(0);

global $modo_pruebas;
$modo_pruebas = 'produccion';

function ___servicios($conf) {
    if(!(isset($conf['servicios']) && $conf['servicios'] != ''))
        return array('codigo_mf_numero' => 9999, 'codigo_mf_texto' => "No se encontro la funcion solicitada para el modulo servicios");
        // Se verifica conexion a internet
        $Context = stream_context_create(array(
            'http' => array(
                'timeout' => 5 
            )
            ));
        global $modo_pruebas;
        if($modo_pruebas != 'produccion')
            $url_test = "http://192.168.1.111/isaac/ws_recargas/distribuidores/" . $conf['distribuidor'] . '.txt';
        else
            $url_test = "http://ta.multifacturas.com/wsta/distribuidores/" . $conf['distribuidor'] . '.txt';
        
        $valor = file_get_contents($url_test, false, $Context);
        if($valor !== false) {
            $id_distribuidor = trim($valor);
            // validar usuario
            $res = saldo_mf($conf['PAC']['usuario'], $conf['PAC']['pass']);
            if($res['codigo_mf_numero'] == '0') {
                // Se agrega el SDK de CEDIX
                require_once 'lib/modulos/servicios/Cedix.php';
                
                $cedix = new Cedix($conf['clientId'], $conf['storeId'], $conf['posId'], $conf['clerkCode'], ($conf['PAC']['PRODUCCION'] == 'SI') ? $conf['urlWS'] : 'http://wspruebas.cedixvirtual.mx/redmas_plat/WebService/Service.asmx?wsdl');
                //======= numeros de respuesta apartir del 200 ====
                $idCase = 0;
                try {
                    switch($conf['servicios']) {
                        //================== FUNCIONES ===============================
                        case 'CONSULTA_SALDOS':
                            $idCase = 1;
                            $respuesta = arrayObj_array($cedix->SaldosDeProveedores());
                            break;
                        case 'CONSULTA_SALDO_PROVEEDOR':
                            $idCase = 2;
                            $respuesta = $cedix->SaldoPorProveedor((int)$conf['proveedor']);
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'LISTA_DETALLADA_PRODUCTOS':
                            $idCase = 3;
                            $respuesta = arrayObj_array($cedix->ListaDetalladaProductos());
                            break;
                        case 'LISTA_PRODUCTOS':
                            $idCase = 4;
                            $respuesta = arrayObj_array($cedix->ListaCompletaProductos());
                            break;
                        case 'LISTA_PRODUCTOS_PROVEEDOR':
                            $idCase = 5;
                            $respuesta = arrayObj_array($cedix->ListaProductosPorProveedor((int)$conf['proveedor']));
                            break;
                        case 'LISTA_PROVEEDORES':
                            $idCase = 6;
                            $respuesta = arrayObj_array($cedix->ListaProveedores());
                            break;
                        case 'LISTA_BANCOS':
                            $idCase = 7;
                            $respuesta = $cedix->ListaDeBancos();
                            break;
                        case 'METODOS_PAGO':
                            $idCase = 8;
                            $respuesta = $cedix->ListaMetodosPago();
                            break;
                        //================== TIEMPO AIRE ===============================
                        case 'ALO':
                            $idCase = 9;
                            $respuesta = $cedix->RecargaALO(
                            (int)$conf['idProducto'],
                            (double)$conf['monto'],
                            $conf['numTel'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'CIERTO':
                            $idCase = 10;
                            $respuesta = $cedix->RecargaCierto(
                            (int)$conf['idProducto'],
                            (double)$conf['monto'],
                            $conf['numTel'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'IUSACELL':
                            $idCase = 11;
                            $respuesta = $cedix->RecargaIusacell(
                            (int)$conf['idProducto'],
                            (double)$conf['monto'],
                            $conf['numTel'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'MAS_RECARGA':
                            $idCase = 12;
                            $respuesta = $cedix->RecargaMasRecarga(
                            (int)$conf['idProducto'],
                            (double)$conf['monto'],
                            $conf['numTel'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'MAZ_TIEMPO':
                            $idCase = 13;
                            $respuesta = $cedix->RecargaMazTiempo(
                            (int)$conf['idProducto'],
                            (double)$conf['monto'],
                            $conf['numTel'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'MOVISTAR':
                            $idCase = 14;
                            $respuesta = $cedix->RecargaMovistar(
                            (int)$conf['idProducto'],
                            (double)$conf['monto'],
                            $conf['numTel'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'NEXTEL':
                            $idCase = 15;
                            $respuesta = $cedix->RecargaNextel(
                            (int)$conf['idProducto'],
                            (double)$conf['monto'],
                            $conf['numTel'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'TELCEL':
                            $idCase = 16;
                            $respuesta = $cedix->RecargaTelcel(
                            (int)$conf['idProducto'],
                            (double)$conf['monto'],
                            $conf['numTel'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'UNEFON':
                            $idCase = 17;
                            $respuesta = $cedix->RecargaUnefon(
                            (int)$conf['idProducto'],
                            (double)$conf['monto'],
                            $conf['numTel'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'VIRGIN_MOBILE':
                            $idCase = 18;
                            $respuesta = $cedix->RecargaVirginMobile(
                            (int)$conf['idProducto'],
                            (double)$conf['monto'],
                            $conf['numTel'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        //================== SERVICIOS ===============================
                        case 'ADOSPACO':
                            $idCase = 19;
                            $respuesta = $cedix->PagoAdospaco(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['cuenta'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'AGUA_DRENAJE_MTY':
                            $idCase = 20;
                            $respuesta = $cedix->PagoAgura_Drenaje_MTY(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'AGUAKAN':
                            $idCase = 21;
                            $respuesta = $cedix->PagoAguakan(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'CFE':
                            $idCase = 22;
                            $respuesta = $cedix->PagoCFE(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'DISH':
                            $idCase = 22;
                            $respuesta = $cedix->PagoDISH(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'FOMERREY':
                            $idCase = 24;
                            $respuesta = $cedix->PagoFomerrey(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'GAS_NATURAL':
                            $idCase = 25;
                            $respuesta = $cedix->PagoGAS(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'GIGACABLE':
                            $idCase = 26;
                            $respuesta = $cedix->PagoGIGACABLE(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'INFONAVIT':
                            $idCase = 27;
                            $respuesta = $cedix->PagoInfonavit(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'MAXCOM':
                            $idCase = 28;
                            $respuesta = $cedix->PagoMAXCOM(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'MEGACABLE':
                            $idCase = 29;
                            $respuesta = $cedix->PagoMEGACABLE(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'MULTIMEDIOS_MONTERREY':
                            $idCase = 30;
                            $respuesta = $cedix->PagoMultimediosMTY(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'SKY':
                            $idCase = 31;
                            $respuesta = $cedix->PagoSKY(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'TELEVIA':
                            $idCase = 32;
                            $respuesta = $cedix->RecargaTelevia(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['referencia'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'TELMEX':
                            $idCase = 33;
                            $respuesta = $cedix->PagoTelmex(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['numTel'],
                            $conf['digito'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'TELNOR':
                            $idCase = 34;
                            $respuesta = $cedix->PagoTelnor(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['codigoBarras'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        //================== TARJETAS DE REGALO ===============================
                        case 'AMERICANISTA':
                            $idCase = 35;
                            $respuesta = $cedix->TarjetaAmericanista(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'ANDALE_LD':
                            $idCase = 36;
                            $respuesta = $cedix->TarjetaAndaleLD(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'BAJALIBROS':
                            $idCase = 37;
                            $respuesta = $cedix->TarjetaBajalibros(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'CINEPOLIS':
                            $idCase = 38;
                            $respuesta = $cedix->TarjetaCinepolis(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'CLUB_PENGUIN':
                            $idCase = 39;
                            $respuesta = $cedix->TarjetaClubPenguin(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'TARJETA_SALUDO':
                            $idCase = 40;
                            $respuesta = $cedix->TarjetaSaludo(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'FACEBOOK':
                            $idCase = 41;
                            $respuesta = $cedix->TarjetaFacebook(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'FACTURA_FIEL':
                            $idCase = 42;
                            $respuesta = $cedix->TarjetaFacturaFiel(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'ITUNES':
                            $idCase = 43;
                            $respuesta = $cedix->TarjetaITUNES(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'KASPERSKY':
                            $idCase = 44;
                            $respuesta = $cedix->TarjetaKaspersky1PC(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'NINTENDO':
                            $idCase = 45;
                            $respuesta = $cedix->TarjetaNintendo(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'PLAYSTATION':
                            $idCase = 46;
                            $respuesta = $cedix->TarjetaPlayStation(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'PLAYSTATION_SEN':
                            $idCase = 47;
                            $respuesta = $cedix->TarjetaPlayStationSEN(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'SKYPE':
                            $idCase = 48;
                            $respuesta = $cedix->TarjetaSKYPE(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'SKYPE_CREDITO_DDP':
                            $idCase = 49;
                            $respuesta = $cedix->TarjetaSKYPE_DDP(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'SONY_PLUS':
                            $idCase = 50;
                            $respuesta = $cedix->TarjetaSonyPlus(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'SUBSCRIPCION_DISH':
                            $idCase = 51;
                            $respuesta = $cedix->TarjetaSubscripcionDish(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            $conf['tel1'],
                            $conf['tel2'],
                            (int)$conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'TRANSACCION_GENERICA':
                            $idCase = 52;
                            $respuesta = $cedix->TransaccionGenerica(
                            (int)$conf['idProducto'],
                            (float)$conf['monto'],
                            (int)$conf['idTransaction'],
                            $conf['ref1'],
                            $conf['ref2'],
                            $conf['ref3']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'LISTA_CATEGORIAS':
                            $idCase = 53;
                            $respuesta = arrayObj_array($cedix->ListaCategorias());
                            break;
                        case 'LISTA_PROVEEDORES_CATEGORIA':
                            $idCase = 54;
                            $respuesta = arrayObj_array($cedix->ListaProveedoresPorCategoria((int)$conf['categoria']));
                            break;
                        case 'CONSULTA_TRANSACCION':
                            $idCase = 55;
                            $respuesta = $cedix->ConsultaTransaccion(
                                $conf['clientId'],
                                $conf['storeId'],
                                $conf['posId'],
                                $conf['clerkCode'],
                                $conf['idProducto'],
                                $conf['monto'],
                                $conf['ref1'],
                                $conf['ref2'],
                                $conf['ref3'],
                                $conf['idTransaction']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        case 'NOTIFICACION_DE_PAGO':
                            $idCase = 56;
                            $respuesta = $cedix->EnviaNotificacionDePago(
                                $conf['monto'],
                                $conf['banco'],
                                $conf['documento'],
                                $conf['fecha'],
                                $conf['cuenta'],
                                $conf['formaPago'],
                                $conf['nombreProveedor']
                            );
                            $respuesta = $respuesta->ToArray();
                            break;
                        //================== NO IDENTIFICADO ===============================
                        default:
                            $respuesta = array('codigo_mf_numero' => '200', 'codigo_mf_texto' => 'Servicio no identificado');
                            break;
                    } // FIN SWITCH
                    $respuesta['codigo_mf_numero'] = ($respuesta['ResponseCode'] != '000' && $respuesta['ResponseCode'] != '011') ? (string)(200 + $idCase) : '0';
                    
                    // Se verifica que sea una transaccion exitosa
                    if($idCase >= 9 && $idCase <= 52 && ($respuesta['codigo_mf_numero'] == '0' || $respuesta['codigo_mf_numero'] = '11')) {
                        // Referencia 1
                        if(isset($conf['numTel']))
                            $conf['ref1'] = $conf['numTel'];
                        if(isset($conf['codigoBarras']))
                            $conf['ref1'] = $conf['codigoBarras'];
                        if(isset($conf['cuenta']))
                            $conf['ref1'] = $conf['cuenta'];
                        if(isset($conf['referencia']))
                            $conf['ref1'] = $conf['referencia'];
                        // Referencia 3
                        if(isset($conf['ref3']))
                            $conf['ref3'] = $conf['ref3'];
                        if(isset($conf['digito']))
                            $conf['ref3'] = $conf['digito'];
                        
                        // Se crea la transaccion
                        $transaccion = array();
                        $transaccion['idCliente'] = isset($conf['clientId']) ? $conf['clientId'] : '';
                        $transaccion['ref1'] = $conf['ref1'];
                        $transaccion['ref2'] =  isset($conf['ref2']) ? $conf['ref2'] : '';
                        $transaccion['ref3'] = $conf['ref3'];
                        $transaccion['autorizacion'] = isset($respuesta['ProviderAuthorization']) ? $respuesta['ProviderAuthorization'] : '';
                        $transaccion['fechaVenta'] = isset($respuesta['TransactionDate']) ? $respuesta['TransactionDate'] : '';
                        $transaccion['producto'] = isset($conf['idProducto']) ? $conf['idProducto'] : '';
                        $transaccion['monto'] = isset($conf['monto']) ? $conf['monto'] : '';
                        
                        if(isset($conf['PAC']['produccion'])) {
                            $transaccion['produccion'] = $conf['PAC']['produccion'] == 'SI' ? 'true' : 'false';
                        }else {
                            $transaccion['produccion'] = 'false';
                        }
                        
                        // Se crea el cliente SOAP
                        global $modo_pruebas;
                        
//                        echo "Modo: $modo_pruebas\r\n";
                        
                        if($modo_pruebas != 'produccion')
                            $url_wsdl = 'http://192.168.1.111/isaac/ws_recargas/index.php?wsdl';
                        else
                            $url_wsdl = "http://ta.multifacturas.com/wsta/index.php?wsdl";
                        
//                        echo "URL Web Service: $url_wsdl\r\n";
                        
                        $wsLog = new nusoap_client($url_wsdl);
                        
                        // Se registra la transaccion
                        $resws = $wsLog->call('RegistraTransaccion', array('distribuidor' => $id_distribuidor, 'transaccion' => $transaccion));
                        
//                        var_dump($resws);
                    }
                    
					if(isset($respuesta['ResponseMessage']) !== false)
						$respuesta['codigo_mf_texto'] = $respuesta['ResponseMessage'];
					else
						$respuesta['codigo_mf_texto'] = 'OK';
                } // FIN TRY
                catch(Exception $e) {
                    $respuesta['codigo_mf_numero'] = (string)(200 + $idCase);
                    $respuesta['codigo_mf_texto'] = $e->getMessage();
                }
            }
            else {
                unset($res['saldo']);
                $respuesta = $res;
            }
        } else {
            $respuesta = array('codigo_mf_numero' => 10, 'codigo_mf_texto' => 'Revise su conexion a internet y/o su numero de distribuidor');
        }
        return $respuesta;
}

function arrayObj_array($array) {
    $array_array = array();
    foreach($array as $obj)
    {
        $array_array[] = $obj->ToArray();
    }
    return $array_array;
}