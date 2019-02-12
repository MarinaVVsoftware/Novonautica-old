<?php
// <!-- phpDesigner :: Timestamp [08/11/2016 12:47:55 p. m.] -->
//  REVISA QUE LA FACTURA ESTE CANCELADA Y LA VUELVE A CANCELAR EN CASO DE FALLA
error_reporting(E_ALL);
//include "../../nusoap/nusoap.php";
global $__mf_constantes__;
// Se carga nusaop
if(!class_exists('nusoap_client'))
{
    mf_carga_libreria($__mf_constantes__['__MF_LIBS_DIR__']."nusoap/nusoap.php");
}

 
function ___consultarestatuscfdi($datos)
{
    if(!file_exists($datos['factura_xml']))
    {  
        $ruta=$datos['factura_xml'];
        $estatus["consulta"]["CodigoEstatus"]="el archivo xml en ruta $ruta NO EXISTE, VERIFICAR RUTA O PERMISOS DE CARPETAS DONDE ESTA ALMACENADO EL ARCHIVO XML";
        $estatus["consulta"]["Estado"]="el archivo xml en ruta $ruta NO EXISTE, VERIFICAR RUTA O PERMISOS DE CARPETAS DONDE ESTA ALMACENADO EL ARCHIVO XML";
	    return $estatus;
	}
    
    $xml_datos=leer_xml($datos['factura_xml']);
    $rfc_emisor=$xml_datos['rfc_emisor'];
    $rfc_receptor=$xml_datos['receptor_rfc'];
    $uuid=$xml_datos['uuid'];
    $monto=$xml_datos['monto'];
    $cadenaqr = "?re=$rfc_emisor&rr=$rfc_receptor&tt=$monto&id=$uuid";
    
    //$cadenaqr_formato="<![CDATA[$cadenaqr]]>";
    $rfc_emisor=trim($rfc_emisor);
    $rfc_receptor=trim($rfc_receptor);
    $importe=trim($monto);
    $uuid=trim($uuid);
    //
    $rfc_emisor=strtoupper($rfc_emisor);
    $rfc_receptor=strtoupper($rfc_receptor);
    $importe=strtoupper($importe);
    $uuid=strtoupper($uuid);
    
    $impo = $importe;
    $impo=sprintf("%.6f", $impo);
    $impo = str_pad($impo,17,"0",STR_PAD_LEFT);
    //PRIMERO BUSCAR EN EL WS DEL SAT POR CURL
 
    $buscar = consultar_estatus_curl($rfc_emisor,$rfc_receptor,$impo,$uuid);
    
    if($datos['PAC']['produccion'] == 'NO')
    {
        echo ff;
        unset($buscar);
        $url = "https://consultaqrfacturaelectronicatest.sw.com.mx/ConsultaCFDIService.svc?singleWsdl";
        $soapclient = new nusoap_client($url,$esWSDL=true);
        $soapclient->soap_defencoding = 'UTF-8'; 
        $soapclient->decode_utf8 = false;
        $impo = $importe;
        $impo=sprintf("%.6f", $impo);
        $impo = str_pad($impo,17,"0",STR_PAD_LEFT);
        $factura = "?re=$rfc_emisor&rr=$rfc_receptor&tt=$impo&id=$uuid";
        $prm = array('expresionImpresa'=>$factura);
        $buscar=$soapclient->call('Consulta',$prm);
    }
    /*
    echo "<pre>";
    print_r($buscar);
    echo "</pre>";
    */

    //AHORA SI LEER RESPUESTA 
    if(isset($buscar['ConsultaResult']['Estado']))
    {
		$estatus["consulta"]["CodigoEstatus"]=$buscar['ConsultaResult']['CodigoEstatus'];
        $estatus["consulta"]["EsCancelable"]=$buscar['ConsultaResult']['EsCancelable'];
        $estatus["consulta"]["Estado"]=$buscar['ConsultaResult']['Estado'];
        $estatus["consulta"]["EstatusCancelacion"]=$buscar['ConsultaResult']['EstatusCancelacion'];
        $estatus["consulta"]["produccion"]=$datos['PAC']['produccion'];
        
        return $estatus;
        
	}
	else
	{  
	   $estatus["consulta"]["CodigoEstatus"]='Desconocido: talvez el servicio del sat esta sin servicio o saturado, intentalo mas tarde';
       $estatus["consulta"]["Estado"]='Desconocido: talvez el servicio del sat esta sin servicio o saturado, intentalo mas tarde';
       $estatus["consulta"]["produccion"]=$datos['PAC']['produccion'];
       return $estatus;
	}

    

}
//////////////////////////////////////////////////////////////////////////////////////////////////////   
function leer_xml($ruta_xml)
{
    ### LEER EL XML ##############################
    $xml = simplexml_load_file($ruta_xml);
    $ns = $xml->getNamespaces(true);
    $xml->registerXPathNamespace('c', $ns['cfdi']);
    $xml->registerXPathNamespace('t', $ns['tfd']);
    foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd)
    {
       $datos['uuid']=(string)$tfd['UUID'];
    }
    foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante)
    {
       $datos['monto']=$cfdiComprobante['Total'];
    }
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $Receptor)
    {
      $datos['receptor_rfc']=$Receptor['Rfc'];
    }

    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $Emisor)
    {
      $datos['rfc_emisor']=$Emisor['Rfc'];
    }
    foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante)
    {
       $datos['total']=$cfdiComprobante['total'];
       $datos['serie']=$cfdiComprobante['serie'];
       $datos['folio']=$cfdiComprobante['folio'];
       $datos['fecha_expedicion']=$cfdiComprobante['fecha'];
    }
    return $datos;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////   
function consultar_estatus_curl($rfc_emisor,$rfc_receptor,$impo,$uuid)
{
   $emisor=$rfc_emisor;
   $receptor=$rfc_receptor;
   $total=$impo;
   $uuid=$uuid;
   //
   $soap = sprintf('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/"><soapenv:Header/><soapenv:Body><tem:Consulta><tem:expresionImpresa>?re=%s&amp;rr=%s&amp;tt=%s&amp;id=%s</tem:expresionImpresa></tem:Consulta></soapenv:Body></soapenv:Envelope>', $emisor,$receptor,$total,$uuid);
   //encabezados
   $headers = [
   'Content-Type: text/xml;charset=utf-8',
   'SOAPAction: http://tempuri.org/IConsultaCFDIService/Consulta',
   'Content-length: '.strlen($soap)
   ];
 
   $url = 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc';
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $soap);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
   $res = curl_exec($ch);
   curl_close($ch);
   $xml = simplexml_load_string($res);
   $data = $xml->children('s', true)->children('', true)->children('', true);
   $data = json_encode($data->children('a', true), JSON_UNESCAPED_UNICODE);
   $datos=json_decode($data,true);
   
   $buscar['ConsultaResult']['CodigoEstatus']=$datos['CodigoEstatus'];
   $buscar['ConsultaResult']['EsCancelable']=$datos['EsCancelable'];;
   $buscar['ConsultaResult']['Estado']=$datos['Estado'];;
   $buscar['ConsultaResult']['EstatusCancelacion']=$datos['EstatusCancelacion'];;
   
   return $buscar; 
    
}

?>
