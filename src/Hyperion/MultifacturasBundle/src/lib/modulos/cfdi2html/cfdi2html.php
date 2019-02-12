<?php
error_reporting(0);
// <!-- phpDesigner :: Timestamp -->17/06/2016 12:34:32 p. m.<!-- /Timestamp -->
function ___cfdi2html($datos)
{
    
    include "num2letras.php";
    include "imprime.php";
    
    //LEER EL XML PARA GENERAR EL QR
    
    $xml_datos=leer_xml($datos['rutaxml']);
    $rfc_emisor=$xml_datos['rfc_emisor'];
    $rfc_receptor=$xml_datos['receptor_rfc'];
    $uuid=$xml_datos['uuid'];
    $monto=$xml_datos['monto'];
    $monto=sprintf("%1.6f",$monto);
    $cadenaqr = "?re=$rfc_emisor&rr=$rfc_receptor&tt=$monto&id=$uuid";
    //ARCHIVO PNG QR
    $archivo_png=str_replace(".xml",".png",$datos['rutaxml']);
    
    if(!file_exists($archivo_png))
    {
        //include_once "../../sdk2.php";
        //include_once "../../lib/modulos/qr/qr.php";
        
        //MODULO MULTIFACTURAS QUE CREA QR PNG DE UN XML CFDI 
        $datosQR['modulo']="qr";
        $datosQR['PAC']['usuario'] = "DEMO700101XXX";
        $datosQR['PAC']['pass'] = "DEMO700101XXX";
        $datosQR['PAC']['produccion'] = "NO";
        $datosQR['cadena']=$cadenaqr;
        $datosQR['archivo_png']=$archivo_png;
        $res = mf_ejecuta_modulo($datosQR);
        //$res = ___qr($datosQR);
        
    }
    //
    
    $xml=$datos['rutaxml'];
    $titulo=$datos['titulo'];
    $tipo=$datos['tipo'];
    $path_logo=$datos['path_logo'];
    $notas=$datos['notas'];
    $color_marco=$datos['color_marco'];
    $color_marco_texto=$datos['color_marco_texto'];
    $color_texto=$datos['color_texto'];
    $fuente_texto=$datos['fuente_texto'];
    $html=imprime_factura($xml,$titulo,$tipo,$path_logo,$notas,$color_marco,$color_marco_texto,$color_texto,$fuente_texto);
    
    return array('html'=>$html);   
}

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
       $datos['monto']=$cfdiComprobante['total'];
    }
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $Receptor)
    {
      $datos['receptor_rfc']=$Receptor['rfc'];
    }

    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $Emisor)
    {
      $datos['rfc_emisor']=$Emisor['rfc'];
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

?>