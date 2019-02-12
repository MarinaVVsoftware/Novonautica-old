<?php
// <!-- phpDesigner :: Timestamp [08/11/2016 12:47:55 p. m.] -->
//  REVISA QUE LA FACTURA ESTE CANCELADA Y LA VUELVE A CANCELAR EN CASO DE FALLA
/*include "masheditor/configuracion.php";

$rfc_emisor='SOHM7509289MA';
$rfc_receptor='XAXX010101000';
$importe='1.00';
$uuid='843CC555-5175-4AAB-98F5-E1A1350DC625';

$res = valida_en_sat($rfc_emisor,$rfc_receptor,$importe,$uuid);
//print_r($buscar);
*/

//function ___valida_en_sat($rfc_emisor,$rfc_receptor,$importe,$uuid) 
function ___valida_sat_cfdi($datos)
{
	// Se carga la factura
	$cfdi = simplexml_load_file($datos['factura_xml']);
	$ns = $cfdi->getNamespaces(true);
    foreach($ns as $prefijo => $uri)
    {
		$cfdi->registerXPathNamespace($prefijo, $uri);
	}
	
	$Emisor = $cfdi->xpath('//cfdi:Emisor');
	$rfc_emisor = (string)$Emisor[0]['rfc'];
	
	$Receptor = $cfdi->xpath('//cfdi:Receptor');
	$rfc_receptor = (string)$Receptor[0]['rfc'];
	
	$Receptor = $cfdi->xpath('//cfdi:Comprobante');
	$importe = (string)$Receptor[0]['total'];
	
	$Timbre = $cfdi->xpath('//tfd:TimbreFiscalDigital');
	$uuid = (string)$Timbre[0]['UUID'];
	
	$params = array('emisor' => $rfc_emisor, 'receptor' => $rfc_receptor, 'total' => $importe, 'UUID' => $uuid);
	//print_r($params);
	
    $rfc_emisor=trim($rfc_emisor);
    $rfc_receptor=trim($rfc_receptor);
    $importe=trim($importe);
    $uuid=trim($uuid);

    $rfc_emisor=strtoupper($rfc_emisor);
    $rfc_receptor=strtoupper($rfc_receptor);
    $importe=strtoupper($importe);
    $uuid=strtoupper($uuid);


    //require_once('nusoap/nusoap.php');
    $url = "https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc?wsdl";
    $soapclient = new nusoap_client($url,$esWSDL=true);
    $soapclient->soap_defencoding = 'UTF-8'; 
    $soapclient->decode_utf8 = false;
    $impo = $importe;
    $impo=sprintf("%.6f", $impo);
    $impo = str_pad($impo,17,"0",STR_PAD_LEFT);

    $factura = "?re=$rfc_emisor&rr=$rfc_receptor&tt=$impo&id=$uuid";
    $prm = array('expresionImpresa'=>$factura);
    $buscar=$soapclient->call('Consulta',$prm);
    
    if(isset($buscar['ConsultaResult']['Estado']))
    {
		return array('Estado' => $buscar['ConsultaResult']['Estado']);
	}
	else
	{
		return array('Estado' => 'Desconocido');
	}

    return $buscar;
//    return $buscar['ConsultaResult']['CodigoEstatus'].'-'.$buscar['ConsultaResult']['Estado'];
    $vig=strtoupper($buscar['ConsultaResult']['Estado']);
    if($vig=="VIGENTE")
    {
        return 'OK';
    }
    else
    {
         return 'ERROR';    
    }
}
// }}}
//////////////////////////////////////////////////////////////////////////////////////////////////////   

// {{{ Convierte EL numero de serie del SAT a formato humano
function convierte($dec) {
    $hex=bcdechex($dec);
    $ser="";
    for ($i=1; $i<strlen($hex); $i=$i+2) {
        $ser.=substr($hex,$i,1);
    }
    return $ser;
}
// }}} Convierte
//////////////////////////////////////////////////////////////////////////////////////////////////////   

// {{{ bcdechex   :  como dechex pero para numeros de precision ilimitada
function bcdechex($dec) {
    $last = bcmod($dec, 16);
    $remain = bcdiv(bcsub($dec, $last), 16);
    if($remain == 0) {
        return dechex($last);
    } else {
        return bcdechex($remain).dechex($last);
    }
}
// }}} bcdechex
//////////////////////////////////////////////////////////////////////////////////////////////////////   

// {{{ get path,  ejecuta el Xpath
function getpath($qry) {
global $xp;
$prm = array();
$nodelist = $xp->query($qry);
foreach ($nodelist as $tmpnode)  {
    $prm[] = trim($tmpnode->nodeValue);
    }
$ret = (sizeof($prm)<=1) ? $prm[0] : $prm;
return($ret);
}
/// }}}}
//////////////////////////////////////////////////////////////////////////////////////////////////////   

// {{{ display_xml_errors
function display_xml_errors() {
    global $texto;
    $lineas = explode("\n", $texto);
    $errors = libxml_get_errors();

   /* echo "<pre>";
    foreach ($errors as $error) {
        echo display_xml_error($error, $lineas);
    }
    echo "</pre>";
*/
    libxml_clear_errors();
}
/// }}}}
//////////////////////////////////////////////////////////////////////////////////////////////////////   

// {{{ display_xml_error
function display_xml_error($error, $lineas) {
    $return  = htmlspecialchars($lineas[$error->line - 1]) . "\n";
    $return .= str_repeat('-', $error->column) . "^\n";

    switch ($error->level) {
        case LIBXML_ERR_WARNING:
           $return .= "Warning $error->code: ";
            break;
         case LIBXML_ERR_ERROR:
           $return .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
           $return .= "Fatal Error $error->code: ";
            break;
    }

    $return .= trim($error->message) .
               "\n  Linea: $error->line" .
               "\n  Columna: $error->column";
//    echo "$return\n\n--------------------------------------------\n\n";
}
//////////////////////////////////////////////////////////////////////////////////////////////////////   
function mkdir_r($dirName, $rights=0777)
{
 
	global $masheditor; 
    $dirs = explode('/', $dirName);
    $dir='';
    $dir=$masheditor["carpeta_instalacion"];
    foreach ($dirs as $part) 
	{
        $dir.=$part.'/';
        if (!is_dir($dir) && strlen($dir)>0)
            mkdir($dir, $rights);
						@chmod($dir, 0777);
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////   


function html_txt($html){
    
    
    if($html=='')
        return $html;
        
    $htmlmd5=md5($html);
    //CACHE        
    $cache_nombre="html_txt:$htmlmd5";
    $cache_nombre_global="html_txt_global";
    
    static $html_txt_cache_static;
    
    if(isset($html_txt_cache_static[$cache_nombre]))
    {
        return $html_txt_cache_static[$cache_nombre];
    }
    if(function_exists('apc_fetch'))
    {
        $text = apc_fetch($cache_nombre);
        $html_txt_cache_static[$cache_nombre]=$text;
    }

    if ($text!='') 
    {
    
    } 
    else 
    { 

        $html=strip_tags($html);
        $buscar = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                       '@<[\\/\\!]*?[^<>]*?>@si',            // Strip out HTML tags
                       '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                       '@<![\\s\\S]*?--[ \\t\\n\\r]*>@'          // Strip multi-line comments including CDATA
        );
        $text = preg_replace($buscar, '', $html);

        $a = array('&nbsp;', '   ', '  ', '  ', '\n\n', '\r\r', '&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', '&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;',  '&Uacute;', '&auml;', '&euml;', '&iuml;', '&ouml;', '&uuml;',  '&ntilde;', '&Ntilde;');
        $b = array(' ', ' ', ' ', ' ', '\n',              '\r', 'á', 'é', 'í', 'ó', 'ú', 'A', 'E', 'I', 'O',                                                                    'U',        'A', 'E', 'I', 'O',             'U',        'ñ', 'Ñ');
        $text=str_replace($a, $b, $text);


        if(function_exists('apc_add'))
        {
            if($html!='')
            {
                $tiempo_cache=3600;
                $html_txt_cache_static[$cache_nombre]=$text;
                apc_add($cache_nombre, $text, $tiempo_cache*1);//1hr
            }            
        }
    }

    

return $text;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////   
function formato_url_mash($html)
{
	return $html;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////   

//////////////////////////////////////////////////////////////////////////////////////////////////////   

?>
