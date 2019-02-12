<?php
function ___validacsd($datos_entrada)
{
    // Se extraen los datos necesarios del parametro de entrada
    $cer = $datos_entrada['cer'];
    $key = $datos_entrada['key'];
    $pass = $datos_entrada['pass'];

    // Se crea la factura sellada sin timbrar
    $factura = DatosFactura($cer, $key, $pass);
    $factura = mf_genera_cfdi($factura);
    $factura = __DIR__ . '/sin_timbrar.xml';

    $resultado = ValidaCSD('SOHM7509', '44a9$Q5q', $factura);

    return array('resultado' => $resultado);
}


function DatosFactura($cer, $key, $pass)
{
    // Se genera el XML sellado sin timbrar
    $datos['version_cfdi']='3.2';
	$datos['conf']['cer'] = $cer;
    $datos['conf']['key'] = $key;
    $datos['conf']['pass'] = $pass;
    $datos['xml_debug'] = __DIR__ . '/sin_timbrar.xml';
    $datos['php_openssl'] = 'SI';

    $datos['factura']['serie'] = 'A'; //opcional
    $datos['factura']['folio'] = '100'; //opcional
    $datos['factura']['fecha_expedicion'] = date('Y-m-d H:i:s', time() - 120);
    $datos['factura']['metodo_pago'] = 'EFECTIVO'; // EFECTIV0, CHEQUE, TARJETA DE CREDITO, TRANSFERENCIA BANCARIA, NO IDENTIFICADO
    $datos['factura']['forma_pago'] = 'PAGO EN UNA SOLA EXHIBICION';  //PAGO EN UNA SOLA EXHIBICION, CREDITO 7 DIAS, CREDITO 15 DIAS, CREDITO 30 DIAS, ETC
    $datos['factura']['tipocomprobante'] = 'ingreso'; //ingreso, egreso
    $datos['factura']['moneda'] = 'MXN'; // MXN USD EUR
    $datos['factura']['tipocambio'] = '1.0000'; // OPCIONAL (MXN = 1.00, OTRAS EJ: USD = 13.45; EUR = 16.86)
    $datos['factura']['LugarExpedicion'] = 'MONTERREY, NUEVO LEON';
    $datos['factura']['RegimenFiscal'] = 'MI REGIMEN';

    $datos['emisor']['rfc'] = 'AAA010101AAA'; //RFC DE PRUEBA
    $datos['emisor']['nombre'] = 'ACCEM SERVICIOS EMPRESARIALES SC';
    $datos['emisor']['DomicilioFiscal']['calle'] = 'JUAREZ';
    $datos['emisor']['DomicilioFiscal']['noExterior'] = '100';
    $datos['emisor']['DomicilioFiscal']['noInterior'] = ''; //(opcional)
    $datos['emisor']['DomicilioFiscal']['colonia'] = 'CENTRO';
    $datos['emisor']['DomicilioFiscal']['localidad'] = 'MONTERREY';
    $datos['emisor']['DomicilioFiscal']['municipio'] = 'MONTERREY'; // o delegacion
    $datos['emisor']['DomicilioFiscal']['estado'] = 'NUEVO LEON';
    $datos['emisor']['DomicilioFiscal']['pais'] = 'MEXICO';
    $datos['emisor']['DomicilioFiscal']['CodigoPostal'] = '01234'; // 5 digitos
    $datos['emisor']['ExpedidoEn']['calle'] = 'HIDALGO';
    $datos['emisor']['ExpedidoEn']['noExterior'] = '240';
    $datos['emisor']['ExpedidoEn']['noInterior'] = ''; //(opcional)
    $datos['emisor']['ExpedidoEn']['colonia'] = 'LAS CUMBRES 3 SECTOR';
    $datos['emisor']['ExpedidoEn']['localidad'] = 'MONTERREY';
    $datos['emisor']['ExpedidoEn']['municipio'] = 'MONTERREY'; // O DELEGACION
    $datos['emisor']['ExpedidoEn']['estado'] = 'NUEVO LEON';
    $datos['emisor']['ExpedidoEn']['pais'] = 'MEXICO';
    $datos['emisor']['ExpedidoEn']['CodigoPostal'] = '64610'; // 5 digitos

    $datos['receptor']['rfc'] = 'XAXX010101000';
    $datos['receptor']['nombre'] = 'PUBLICO EN GENERAL';

    $concepto['cantidad'] = 1;
    $concepto['unidad'] = 'PIEZA';
    $concepto['ID'] = "COD1";
    $concepto['descripcion'] = "PRODUCTO PRUEBA 2 para el rfc OOYL940109213 $i";
    $concepto['valorunitario'] = '100.00'; // SIN IVA
    $concepto['importe'] = '100.00';
    $datos['conceptos'][] = $concepto;

    $datos['factura']['subtotal'] = 1100.00; // sin impuestos
    $datos['factura']['descuento'] = 100.00; // descuento sin impuestos
    $datos['factura']['total'] = 1160.00; // total incluyendo impuestos
    $datos['factura']['subtotal'] = 1000.00; // sin impuestos
    $datos['factura']['descuento'] = 0.00; // descuento sin impuestos
    $datos['factura']['total'] = 1160.00; // total incluyendo impuestos

    $translado1['impuesto'] = 'IVA';
    $translado1['tasa'] = '16';
    $translado1['importe'] = 160.00; // iva de los productos facturados
    $datos['impuestos']['translados'][0] = $translado1;

    return $datos;
}

/**
 * @param $usuario string Usuario
 * @param $clave string Contraseña
 * @param $xml string Ruta del XML
 * @return bool
 */
function ValidaCSD($usuario, $clave, $xml)
{
    // Se lee el xml
    $xml = file_get_contents($xml);
    // Se elimina el UTF-8
    $xml = utf8_decode($xml);

    // Se crean los parametros a enviar
    $parametros = array(
        'usuario' => $usuario,
        'contrasena' => $clave,
        'xmlComprobante' => $xml
    );

    // Se crea el cliente SOAP
    $soapclient = new nusoap_client('https://invoiceone.mx/TimbreCFDI/TimbreCFDI.asmx?wsdl', $esWSDL = true);

    // Se ejecuta la prueba de timbrado
    $result = $soapclient->call('ObtenerCFDIPrueba', $parametros);

    if(array_key_exists('faultstring', $result))
        $respuesta = $result['faultstring'];
    if(array_key_exists('ObtenerCFDIPruebaResult', $result)) {
        $respuesta = array_key_exists('Xml', $result['ObtenerCFDIPruebaResult']) === true ? 'Certificados validos' : 'Certificados invalidos';
    }

    return $respuesta;
}