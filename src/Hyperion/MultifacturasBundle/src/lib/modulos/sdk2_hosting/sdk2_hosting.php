<?php
function ___sdk2_hosting($datos=null)
{
	global $__mf_constantes__;
    $php_minimo=53;

    list($uno,$dos,$tres)=explode('.',PHP_VERSION);
    $php_actual=intval("$uno$dos");

    $pruebas['version_php'] = PHP_VERSION;
    
    if($php_actual<$php_minimo)
    {
        $pruebas['version_php'] = 'ERROR, VERSION MINIMA 5.3';
    }
    else
    {
        $pruebas['version_php'] = 'OK';
    }


    if (function_exists('ioncube_loader_version'))
{
        $pruebas['ioncube'] = 'OK';
}
else
{
        $pruebas['ioncube'] = 'IONCUBE NO INSTALADO';
}
/////////////////////////////////////////////////
////        PRUEBA XSL 
/////////////////////////////////////////////////
$xsl='';
    if(function_exists('shell_exec'))
    {
        $respuesta=shell_exec('xsltproc');
        if(strpos($respuesta,'--verbose'))
        {
            $xsl.="OK";
			$pruebas['extension_xsl'] = 'OK';
        }
        else
        {
            $pruebas['extension_xsl'] = 'ERROR : Requiere xsltproc';
        }
        
    }
    else
    {
		$pruebas['extension_xsl'] = 'ERROR : permiso shell_exec';
    }

    if(class_exists("DOMDocument")==true AND class_exists("XSLTProcessor")==true)
    {
            $xsl.="OK";
			$pruebas['extension_dom'] = 'OK';
    }
    else
    {
            $pruebas['extension_dom'] = 'ERROR : Requiere libreria XSL y DOM en php';
    }
    
    if($xsl=='')
    {
        $resultado='ERROR';
    }
    else
    {
        $resultado='OK';
    }

/////////////////////////////////////////////////
////        PRUEBA XSD 
/////////////////////////////////////////////////
    if(file_exists($__mf_constantes__['__MF_LIBS_DIR__']."sat/xsd33/cfdv33.xsd")==true)
    {
        $pruebas['validar_xsd'] = $resultado;
    }
    else
    {
        $pruebas['validar_xsd'] = 'ESTADO : ADVERTENCIA!!!! falta la carpeta xsd, sin ella no se puede validar el XML antes de timbrar haciendo mas probable consumas timbres inecesarios';
    }


/////////////////////////////////////////////////
////        PRUEBA OPENSSL 
/////////////////////////////////////////////////
$openssl='';
    if(function_exists('shell_exec'))
    {
        $respuesta=shell_exec('openssl --');
        if(strpos($respuesta,'x509'))
        {
            $openssl.="OK";
			$pruebas['openssl'] = 'OK';
        }
        else
        {
            $pruebas['openssl'] = 'ERROR : Requiere ejecutable openssl';
        }
        
    }
    else
    {
		$pruebas['openssl'] = 'ERROR : permiso shell_exec';
    }

    if(function_exists('openssl_sign')==true)
    {
            $openssl.="OK";
			$pruebas['openssl_sign'] = 'OK';
    }
    else
    {
            $pruebas['openssl_sign'] = 'ERROR : Requiere libreria OpenSSL para PHP';
    }
    
    if($openssl=='')
    {
        $resultado='ERROR';
    }
    else
    {
        $resultado='OK';
    }

/////////////////////////////////////////////////
////        CONEXION AL WEB SERVICE 
/////////////////////////////////////////////////
if(file_exists($__mf_constantes__['__MF_LIBS_DIR__']."sat/xslt33/cadenaoriginal_3_3.xslt")==true )
{
    $pruebas['archivos_xslt'] = 'OK';
} 
else
{
    $pruebas['archivos_xslt'] = 'ERROR FALTA CARPETA XSLT SIN ELLA NO SE PUEDE GENERAR EL SELLO';
}

$rand=rand(111,999).".txt";
file_put_contents($__mf_constantes__['__MF_SDK_TMP__'].$rand,$rand);
file_put_contents($__mf_constantes__['__MF_LIBS_DIR__'].$rand,$rand);

$tmp=file_get_contents($__mf_constantes__['__MF_SDK_TMP__'].$rand);
$lib=file_get_contents($__mf_constantes__['__MF_LIBS_DIR__'].$rand);

if($tmp==$rand)
{
    $tmp='OK';
	$pruebas['permisos_tmp'] = 'OK ';
} 
else
{
    $pruebas['permisos_tmp'] = 'ERROR NO HAY PERMISO DE ESCRITURA EN CARPETA "tmp"';
}

if($lib==$rand)
{
    $lib='OK';
	$pruebas['permisos_lib'] = 'OK';
} 
else
{
    $pruebas['permisos_lib'] = 'ERROR NO HAY PERMISO DE ESCRITURA EN CARPETA "lib"';
}

if(function_exists('simplexml_load_file')==true)
{
		$pruebas['lib_simplexml'] = 'OK';
}
else
{
		$pruebas['lib_simplexml'] = 'ERROR : Requiere libreria simplexml para PHP';
}



if(class_exists("DOMDocument")==true )
{
		$pruebas['lib_domdocument'] = 'OK';
}
else
{
		$pruebas['lib_domdocument'] = 'RECOMENDACION : Requiere libreria DOMDocument en PHP para mejorar la velocidad de timbrado';
}

$res= file_get_contents("http://pac1.multifacturas.com/pac?wsdl");
if(strlen($res)>5000)
{
		$pruebas['prueba_servidor'] = 'OK';
}
else
{
		$pruebas['prueba_servidor'] = 'FALLA comunicacion al servidor de timbrado, revise firewall o conexion a internet';
}

/////////////////////////////////////////////////
////        GENERACION PEM 
/////////////////////////////////////////////////
    $error='OK';
    if(file_exists($__mf_constantes__['__MF_SDK_DIR__']."sdk15.php") && file_exists($__mf_constantes__['__MF_SDK_DIR__']."sdk17.php") && file_exists($__mf_constantes__['__MF_SDK_DIR__']."sdk25.php") && file_exists($__mf_constantes__['__MF_SDK_DIR__']."sdk27.php"))
    {
        
        $cer= file_get_contents("http://app.multifacturas.com/app/pruebas/aaa010101aaa.cer");
        $key= file_get_contents("http://app.multifacturas.com/app/pruebas/aaa010101aaa.key");
        file_put_contents($__mf_constantes__['__MF_SDK_DIR__']."certificados/AAA010101AAA.cer",$cer);
        file_put_contents($__mf_constantes__['__MF_SDK_DIR__']."certificados/AAA010101AAA.key",$key);
        //unlink('certificados/AAA010101AAA.cer.pem');
        //unlink('certificados/AAA010101AAA.key.pem');
        /*$datos['conf']['cer'] = 'certificados/AAA010101AAA.cer.pem';
        $datos['conf']['key'] = 'certificados/AAA010101AAA.key.pem';
        $datos['conf']['pass'] = '12345678a';*/
        
        if(file_exists($__mf_constantes__['__MF_SDK_DIR__']."certificados/AAA010101AAA.cer.pem"))
        {
            $pruebas['c_cer_pem'] = 'OK';
        }
        else
        {
            $error.='SI';
			$pruebas['c_cer_pem'] = 'ERROR GENERANDO ARCHIVO .CER.PEM';
        }

        if(file_exists($__mf_constantes__['__MF_SDK_DIR__']."certificados/AAA010101AAA.key.pem"))
        {
            $pruebas['c_key_pem'] = 'OK';
        }
        else
        {
            $error.='SI';
			$pruebas['c_key_pem'] = 'ERROR GENERANDO ARCHIVO .KEY.PEM ';
        }
        
    }
    else
    {
        $error.='SI';
        $pruebas['c_cer_pem'] = 'ERROR : <b>FALTA alguno de los archivos sdk15.php, sdk17.php, sdk25.php o sdk27.php para realizar la última prueba';
    }
    
    if($error!='OK')
    {
        $pruebas['c_key_pem'] = 'ERROR GRAVE, NO SE PUEDEN PROCESAR LOS CERTIFICADOS';
    }
//return $pruebas;
return $pruebas;
}
 ?>