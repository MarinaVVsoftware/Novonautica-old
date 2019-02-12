<?php

function mf_init_nodo_retencion(array &$datos)
{
    global $__mf_constantes__;

    // Ruta XSD de Retenciones
    $__mf_constantes__['__MF_XSD_RET_DIR__'] = $__mf_constantes__['__MF_NODOS_DIR__'] . $__mf_constantes__['__MF_TIPO_DOCUMENTO__'] . '/sat/xsd/';

    // Ruta XSLT de Retenciones
    $__mf_constantes__['__MF_XSLT_RET_DIR__'] = $__mf_constantes__['__MF_NODOS_DIR__'] . $__mf_constantes__['__MF_TIPO_DOCUMENTO__'] . '/sat/xslt/';

    // Ruta de la carpeta complementos
    $__mf_constantes__['__MF_PRE_DIR__'] = $__mf_constantes__['__MF_NODOS_DIR__'] . $__mf_constantes__['__MF_TIPO_DOCUMENTO__'] . '/1pre/';
    // Ruta de la carpeta complementos
    $__mf_constantes__['__MF_INTER_DIR__'] = $__mf_constantes__['__MF_NODOS_DIR__'] . $__mf_constantes__['__MF_TIPO_DOCUMENTO__'] . '/2intermedio/';
    // Ruta de la carpeta complementos
    $__mf_constantes__['__MF_POST_DIR__'] = $__mf_constantes__['__MF_NODOS_DIR__'] . $__mf_constantes__['__MF_TIPO_DOCUMENTO__'] . '/3post/';
}

function mf_nodo_retencion($datos,$produccion='NO')
{
    global $__mf_constantes__;
    init_sdk($datos);

// DA FORMATO
    if(!isset($datos['html_a_txt']))
    {
        $datos['html_a_txt']='';
    }
    if($datos['html_a_txt']=='SI')
    {
        $datos= array_map_recursive('cfd_fix_dato_xml_html_txt', $datos);
    }

    if(!isset($datos['remueve_acentos']))
    {
        $datos['remueve_acentos']='';
    }
    if($datos['remueve_acentos']=='SI')
    {
        $datos= array_map_recursive('cfd_fix_dato_xml_acentos', $datos);
    }
    else
    {
        $datos= array_map_recursive('cfd_fix_dato_xml', $datos);
    }

//LEE VARIABLES
    if(!isset($datos['SDK']['ruta']))
    {
        $datos['SDK']['ruta']='';
    }
    $ruta=$datos['SDK']['ruta'];
    $ruta=str_replace('\\','/',$ruta);
    $cer=$datos['conf']['cer'];

    $certificado=cfd_certificado_pub($cer);

    $usuario = $datos['PAC']['usuario'];
    $clave   = $datos['PAC']['pass'];

    $codigo_mf_numero=$res_saldo['codigo_mf_numero'];

    if($codigo_mf_numero>0)
    {
        $res['codigo_mf_numero']=$res_saldo['codigo_mf_numero'];
        $res['codigo_mf_texto']=$res_saldo['codigo_mf_texto'];
        return $res;
    }

    if($datos['PAC']['produccion']!='SI')
    {
        $datos['PAC']['produccion']='NO';

    }

    $produccion=$datos['PAC']['produccion'];
    
    
    
    if(!file_exists("$cer.txt"))
    {
        mf_prepara_certificados($datos);
    }
    

    if(file_exists("$cer.txt"))
    {
        $numero_cer=file_get_contents("$cer.txt");
    }
    else
    {
        $res['produccion']=$produccion;
        $res['codigo_mf_numero']=7;
        $res['codigo_mf_texto']='CERTIFICADO NO VALIDO, NO SE PUDO LEER EL NUMERO DEL CERTIFICADO';
        $res['cancelada']='SI';
        $res['servidor']=0;
        return $res;
    }

    // Complementos
    $complementos = '';
    // namespaces
    $ns_dividendos = '';
    $sl_dividendos = '';

    // Dividendos
    if(isset($datos['dividendos']))
    {
        $ns_dividendos = 'xmlns:dividendos="http://www.sat.gob.mx/esquemas/retencionpago/1/dividendos"';
        $sl_dividendos = "http://www.sat.gob.mx/esquemas/retencionpago/1/dividendos http://www.sat.gob.mx/esquemas/retencionpago/1/dividendos/dividendos.xsd ";

        $datosDividendos = $datos['dividendos'];
        $nodoDividendos = '<dividendos:Dividendos Version="1.0">';

        if(isset($datosDividendos['DividOUtil']))
        {
            $atrs = mf_atributos_nodo($datosDividendos['DividOUtil'], '');
            $nodoDividendos .= "<dividendos:DividOUtil $atrs/>";
        }

        if(isset($datosDividendos['Remanente']))
        {
            $atrs = mf_atributos_nodo($datosDividendos['Remanente'], '');
            $nodoDividendos .= "<dividendos:Remanente $atrs/>";
        }

        $nodoDividendos .= '</dividendos:Dividendos>';

        $complementos = $nodoDividendos;
    }

    // Nodo Retenciones
    $nodoRetenciones = '';
    if(isset($datos['factura']))
    {
        $datos['factura']['NumCert'] = $numero_cer;
        $datos['factura']['Cert'] = $certificado;
        $datosRetencion = $datos['factura'];
        $atrsRetenciones = 'Version=\'1.0\' ' . mf_atributos_nodo($datosRetencion, '') . '{SELLO} ';
        $atrsRetenciones .= "xmlns:retenciones=\"http://www.sat.gob.mx/esquemas/retencionpago/1\" $ns_dividendos xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sat.gob.mx/esquemas/retencionpago/1 http://www.sat.gob.mx/esquemas/retencionpago/1/retencionpagov1.xsd $sl_dividendos\" ";
        $nodoRetenciones = "<?xml version=\"1.0\" encoding=\"utf-8\"?><retenciones:Retenciones $atrsRetenciones>";
    }

    // Nodo emisor
    if(isset($datos['emisor']))
    {
        $datosEmisor = $datos['emisor'];
        $atrsEmisor = mf_atributos_nodo($datosEmisor, '');
        $nodoRetenciones .= "<retenciones:Emisor $atrsEmisor/>";
    }

    // Nodo Receptor
    if(isset($datos['receptor']))
    {
        $datosReceptor = $datos['receptor'];
        $atrsReceptor = mf_atributos_nodo($datosReceptor, '');
        $nodoRetenciones .= "<retenciones:Receptor $atrsReceptor>";

        // Nodo Nacional
        if (isset($datos['receptor']['Nacional']))
        {
            $datosNacional = $datos['receptor']['Nacional'];
            $atrsNacional = mf_atributos_nodo($datosNacional, '');
            $nodoRetenciones .= "<retenciones:Nacional $atrsNacional/>";
        }

        // Nodo Extrangero
        if (isset($datos['receptor']['Extranjero']))
        {
            $datosExtranjero = $datos['receptor']['Extranjero'];
            $atrsExtranjero = mf_atributos_nodo($datosExtranjero, '');
            $nodoRetenciones .= "<retenciones:Extranjero $atrsExtranjero/>";
        }
        $nodoRetenciones .= "</retenciones:Receptor>";
    }

    // Nodo Periodo
    if(isset($datos['periodo']))
    {
        $datosPeriodo = $datos['periodo'];
        $atrsPeriodo = mf_atributos_nodo($datosPeriodo, '');
        $nodoRetenciones .= "<retenciones:Periodo $atrsPeriodo/>";
    }

    // Nodo Totales
    if(isset($datos['totales']))
    {
        $datosTotales = $datos['totales'];
        $atrsTotales = mf_atributos_nodo($datosTotales, '');
        $nodoRetenciones .= "<retenciones:Totales $atrsTotales>";

        // Nodo ImpRetenidos
        if(isset($datosTotales['ImpRetenidos']))
        {
            $datosImpRetenidos = $datosTotales['ImpRetenidos'];
            foreach($datosImpRetenidos as $idx => $nodo)
            {
                $atrImpRet = mf_atributos_nodo($nodo, '');
                $nodoRetenciones .= "<retenciones:ImpRetenidos $atrImpRet/>";
            }
        }

        $nodoRetenciones .= "</retenciones:Totales>";
    }

    // Se agregan los complementos
    if($complementos != '')
    {
        $nodoRetenciones .= "<retenciones:Complemento>$complementos</retenciones:Complemento>";
    }

    $nodoRetenciones .= "</retenciones:Retenciones>";
    $xml_iso8859 = $nodoRetenciones;
// MODULO SELLO
//    $xslt = 'xslt/retenciones.xslt';
    global $__mf_constantes__;

    $xslt = $__mf_constantes__['__MF_XSLT_RET_DIR__'] . 'retenciones.xslt';
    $xml_iso8859 = utf8_encode($xml_iso8859);
    $xml_iso8859=contabilidad_sello($datos,$xml_iso8859,$xslt);
//    file_put_contents("c:\\multifacturas_sdk\\dividendos.xml",utf8_encode($xml_iso8859));
//$xml_iso8859=utf8_encode($xml_iso8859);
    //file_put_contents('tmp/retenciones.xml',utf8_encode($xml_iso8859));
    file_put_contents($__mf_constantes__['__MF_SDK_TMP__'] . 'retenciones.xml',utf8_encode($xml_iso8859));




//    $datos['xml']=utf8_decode($xml_iso8859);
    //$datos['xml']=$xml_iso8859;

    //$res=mf_genera_cfdi($datos);
    if(!isset($datos['PAC']['usuario']))
    {
        $datos['PAC']['usuario']='DEMO700101XXX';
    }
    if(!isset($datos['PAC']['pass']))
    {
        $datos['PAC']['pass']='DEMO700101XXX';
    }
    $res=mf_timbrar_retencion(rand(1, 10), $datos['PAC']['usuario'],$datos['PAC']['pass'], $xml_iso8859);

    if($res['codigo_mf_numero'] == 0 || $res['codigo_mf_numero'] == '0')
    {
        $res['cfdi']=base64_decode($res['cfdi']);
        file_put_contents($datos['cfdi'], $res['cfdi']);
        $ruta_png = substr($datos['cfdi'], 0, -3) . 'png';
        file_put_contents($ruta_png, base64_decode($res['png']));
    }
    else
    {
        $res['png'] = '';
    }
    _cfdi_almacena_error_();
    return $res;

}