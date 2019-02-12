<?php
// <!-- phpDesigner :: Timestamp [30/11/2016 04:11:55 p. m.] -->
//error_reporting(E_ALL);

function _inim_imprime()
{

    $js="
    <script>
        function dbme_muestra_pdf(id,pdf)
        {
            var info = getAcrobatInfo();
            if(info.acrobat==false)
            {
                //alert('no se ve');
                $('#iddbme_pdf_'+id).html(pdf);
                
                $('#iddbme_pdf_'+id).html(\"<iframe width='90%' height='420' src='{URL}/masheditor/libs/pdfjs/web/viewer.html?file=\"+pdf+\"'></iframe>\");
                
            }
            else 
            {
                $('#iddbme_pdf_'+id).html(\"<iframe width='90%' height='420' src='\"+pdf+\"'></iframe>\");
            }
        }
    
    
        //
        // http://thecodeabode.blogspot.com
        // @author: Ben Kitzelman
        // @license: FreeBSD: (http://opensource.org/licenses/BSD-2-Clause) Do whatever you like with it
        // @updated: 03-03-2013
        //
        var getAcrobatInfo = function() {
         
        var getBrowserName = function() {
        return this.name = this.name || function() {
        var userAgent = navigator ? navigator.userAgent.toLowerCase() : 'other';
         
        if(userAgent.indexOf('chrome') > -1) return 'chrome';
        else if(userAgent.indexOf('safari') > -1) return 'safari';
        else if(userAgent.indexOf('msie') > -1) return 'ie';
        else if(userAgent.indexOf('firefox') > -1) return 'firefox';
        return userAgent;
        }();
        };
         
        var getActiveXObject = function(name) {
        try { return new ActiveXObject(name); } catch(e) {}
        };
         
        var getNavigatorPlugin = function(name) {
        for(key in navigator.plugins) {
        var plugin = navigator.plugins[key];
        if(plugin.name == name) return plugin;
        }
        };
         
        var getPDFPlugin = function() {
        return this.plugin = this.plugin || function() {
        if(getBrowserName() == 'ie') {
        //
        // load the activeX control
        // AcroPDF.PDF is used by version 7 and later
        // PDF.PdfCtrl is used by version 6 and earlier
        return getActiveXObject('AcroPDF.PDF') || getActiveXObject('PDF.PdfCtrl');
        }
        else {
        return getNavigatorPlugin('Adobe Acrobat') || getNavigatorPlugin('Chrome PDF Viewer') || getNavigatorPlugin('WebKit built-in PDF');
        }
        }();
        };
         
        var isAcrobatInstalled = function() {
        return !!getPDFPlugin();
        };
         
        var getAcrobatVersion = function() {
        try {
        var plugin = getPDFPlugin();
         
        if(getBrowserName() == 'ie') {
        var versions = plugin.GetVersions().split(',');
        var latest = versions[0].split('=');
        return parseFloat(latest[1]);
        }
         
        if(plugin.version) return parseInt(plugin.version);
        return plugin.name
        }
        catch(e) {
        return null;
        }
        }
         
        //
        // The returned object
        //
        return {
        browser: getBrowserName(),
        acrobat: isAcrobatInstalled() ? 'installed' : false,
        acrobatVersion: getAcrobatVersion()
        };
        };    
        </script>
    ";
    AgregarJS_mash($js);    
}


///////////////////////////////////////////////////////////////////////////////
function imprime_ticket($idfactura,$nota_impresa)
{
    $js='<link rel="stylesheet" href="{URL}/factura.css?tmp=213" type="text/css"/> 
    <style>
    body{
        text-transform: uppercase;
      font-size: 12px !important;
      margin : 0px !important;
      padding: 0px !important;
    }
    .titulo{
      font-size: 22px !important;   
      text-align: center;
    }
    .cabecera{
      font-size: 10px !important;   
    }
    .codigo{
      font-size: 18px !important;   
      text-align: left;
    }
    
    </style>
    
    ';
    if(function_exists('AgregarJS_mash')==true)
    {
        AgregarJS_mash($js);
    }
    else
    {
        $valor.='<head><link rel="stylesheet" href="factura.css?tmp=213" type="text/css"/> </head>';
    }



    $sql="
        SELECT 
          `multi_config`.EMPRESA_NOMBRE,
          `multi_config`.LOGO,
          `multi_config`.EMISOR_NOMBRE,
          `multi_config`.EMISOR_RFC,
          `multi_config`.EMISOR_EXP_CALLE,
          `multi_config`.EMISOR_EXP_NUMERO_EXTERIOR,
          `multi_config`.EMISOR_EXP_COLONIA,
          `multi_config`.EMISOR_EXP_NUMERO_INTERIOR,
          `multi_config`.EMISOR_EXP_LOCALIDAD,
          `multi_config`.EMISOR_EXP_MUNICIPIO,
          `multi_config`.EMISOR_EXP_ESTADO,
          `multi_config`.EMISOR_EXP_PAIS,
          `multi_config`.EMISOR_EXP_CP,
          `multi_config`.Pagina_WEB,
          `multi_config`.Telefonos,
          `multi_config`.Correos,
          `multi_config`.pagina_tickets,
          multi_facturas.SERIE,
          multi_facturas.FOLIO,
          multi_facturas.FECHA,
          multi_facturas.TOTAL,
          multi_facturas.codigo_ticket
        FROM
          multi_facturas
          LEFT OUTER JOIN `multi_config` ON (multi_facturas.idconfig = `multi_config`.idconfig)
        WHERE
          (multi_facturas.idfactura = $idfactura)    
    ";

    $datosticket=lee_sql_mash($sql);


    $codigo_ticket=$datosticket['codigo_ticket'];
    $logo=$datosticket['LOGO'];
    $EMPRESA_NOMBRE=$datosticket['EMPRESA_NOMBRE'];
    $EMISOR_NOMBRE=$datosticket['EMISOR_NOMBRE'];
    $EMISOR_RFC=$datosticket['EMISOR_RFC'];
    $RFC2=$datosticket['RFC'];
    $EMISOR_EXP_CALLE=$datosticket['EMISOR_EXP_CALLE'];
    $EMISOR_EXP_NUMERO_EXTERIOR=$datosticket['EMISOR_EXP_NUMERO_EXTERIOR'];
    $EMISOR_EXP_COLONIA=$datosticket['EMISOR_EXP_COLONIA'];
    $EMISOR_EXP_NUMERO_INTERIOR=$datosticket['EMISOR_EXP_NUMERO_INTERIOR'];
    $EMISOR_EXP_LOCALIDAD=$datosticket['EMISOR_EXP_LOCALIDAD'];
    $EMISOR_EXP_MUNICIPIO=$datosticket['EMISOR_EXP_MUNICIPIO'];
    $EMISOR_EXP_ESTADO=$datosticket['EMISOR_EXP_ESTADO'];
    $EMISOR_EXP_PAIS=$datosticket['EMISOR_EXP_PAIS'];
    $EMISOR_EXP_CP=$datosticket['EMISOR_EXP_CP'];
    $Pagina_WEB=$datosticket['Pagina_WEB'];
    $Telefonos=$datosticket['Telefonos'];
    $SERIE=$datosticket['SERIE'];
    $FOLIO=$datosticket['FOLIO'];
    $FECHA=$datosticket['FECHA'];
    $TOTAL=$datosticket['TOTAL'];
    $Correos=$datosticket['Correos'];
    $pagina_factura=$datosticket['pagina_tickets'];

    $domicilio_fiscal="
    $EMISOR_NOMBRE <br/>
    $EMISOR_EXP_CALLE $EMISOR_EXP_NUMERO_EXTERIOR  $EMISOR_EXP_NUMERO_INTERIOR, 
    $EMISOR_EXP_COLONIA,  
    $EMISOR_EXP_LOCALIDAD $EMISOR_EXP_MUNICIPIO CP $EMISOR_EXP_CP 
    $EMISOR_EXP_PAIS 
 <br/>    
    $Telefonos  $Pagina_WEB $Correos <br/>
    FECHA : $FECHA<br/>
    ";
    $TICKET_RFC=$EMISOR_RFC;
    if($TICKET_RFC=='')
    {
        $TICKET_RFC=$RFC2;
    }

$factura="
    <div class='codigo'>
    <b>FACTURA EN LINEA</b>
    <br/> 
     <b>RFC : $TICKET_RFC<b> <br/> 
    TICKET : $SERIE$FOLIO <br/> 
    CODIGO $codigo_ticket <br/> 
    $pagina_factura
    </div>
    <hr/> 
";

    if(!file_exists($logo))
    {
        $logo="c:/cfdipdf/transparente.gif";
    }
    else
    {
        $conflogo['max']=200;
        if(function_exists('ver_imagen_mash'))
        {
            $logo=ver_imagen_mash($logo,200,0,$conflogo);
        }
        else
        {
            $logo=$logo;
        }
    }
    $valor="
    
    <center><img src='{URL}/$logo'></center>
    <div class='titulo'>$EMPRESA_NOMBRE</div>
    <span class='cabecera'>
    $domicilio_fiscal
    </span>
    <hr/>
    ";
    
    $sql="
    SELECT *
    FROM
      `multi_facturas_detalles`
    WHERE
      (`multi_facturas_detalles`.idfactura = $idfactura)
    ";
    $res=mysql_query($sql);
    while($datos=mysql_fetch_array($res))
    {
        
        $CODIGO=$datos['CODIGO'];
        $DESCRIPCION=$datos['DESCRIPCION'];
        $PRECIO_PUBLICO=$datos['PRECIO_PUBLICO'];
        $PRECIO_PUBLICO_UNITARIO=$datos['PRECIO_PUBLICO_UNITARIO'];
        $CANTIDAD=$datos['CANTIDAD'];
        $IVA_TASA=$datos['IVA_TASA'];
        $UNIDAD=$datos['UNIDAD'];        
        $PRECIO_PUBLICO=$PRECIO_PUBLICO+$PRECIO_PUBLICO*$IVA_TASA/100;
        $PRECIO_PUBLICO=number_format($PRECIO_PUBLICO,2);

          $valor.="<br/>* $DESCRIPCION 
          <br/> 
          <div style='text-align: right;'>$CANTIDAD UNIDAD --- $$PRECIO_PUBLICO</div>
  ";

    }

    
    $valor.="
    <hr/>
    <div class='titulo'>TOTAL $$TOTAL</div>
    <hr/>
    $factura
    ";
    
        
    return $valor;
}
///////////////////////////////////////////////////////////////////////////////
function imprime_factura($xml_archivo,$titulo,$tipo_factura,$logo,$nota_impresa)
{
    $version=''; //declarar version xml cfdi
    
        $js='<link rel="stylesheet" href="{URL}/factura.css?tmp=213" type="text/css"/> ';
        if(function_exists('AgregarJS_mash')==true)
        {
            AgregarJS_mash($js);
        }
        else
        {
            $valor.='<head><link rel="stylesheet" href="factura.css?tmp=213" type="text/css"/> </head>';
        }
//$xml_archivo='ejemplo_nomina.xml';
//$tipo_factura='NOMINA';
//echo $xml_archivo;
//$xml_archivo='ejemplo_honorarios.xml';
//$tipo_factura='ARRENDAMIENTO';
    if(file_exists($xml_archivo)==false)
    {
        return 'ERROR 1, NO EXISTE XML, MUY  POSIBLEMENTE ES UNA PRUEBA FALLIDA';
    }
    if(filesize($xml_archivo)<100)
    {
        return 'ERROR 2, XML INVALIDO';        
    }

    $xml = simplexml_load_file($xml_archivo);
    $ns = $xml->getNamespaces(true);
    foreach($ns as $prefijo => $uri)
    {
		$xml->registerXPathNamespace($prefijo, $uri);
	}


    /*$ns = $xml->getNamespaces(true);

    $xml->registerXPathNamespace('c', $ns['cfdi']);
    $xml->registerXPathNamespace('t', $ns['tfd']);*/
    
    
//    $xml->registerXPathNamespace('i', $ns['implocal']);

/*
    $xml->registerXPathNamespace('c', $ns['cfdi']);
    $xml->registerXPathNamespace('t', $ns['tfd']);
*/


     
     
    //EMPIEZO A LEER LA INFORMACION DEL CFDI E IMPRIMIRLA
    foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante)
    {
        $version= $cfdiComprobante['version'];  //3.2
       
        if($version =='')
            $version= $cfdiComprobante['Version'];  //3.3
        
        if($version =='')    
                echo "xml no valido";
                
        if($version=="3.2")
        {
            $fecha_expedicion= $cfdiComprobante['fecha'];
            //TODO: aki etiketa de cuenta en cheke
            $metodo_pago= autoformato_impresion($cfdiComprobante['metodoDePago']);
            $metodo_pago=formato_metodo_pago($metodo_pago);
            
            $sello= $cfdiComprobante['sello'];
            $total=$cfdiComprobante['total'];
            $total_=number_format((string)$total,2);
            $Moneda=$cfdiComprobante['Moneda'];
            
            $subtotal=$cfdiComprobante['subTotal'];
            $subtotal_=number_format((string)$subtotal,2);
            
            $descuento=$cfdiComprobante['descuento'];
            $descuento_=number_format((string)$descuento,2);
          
            $serie=$cfdiComprobante['serie'];
            $folio=$cfdiComprobante['folio'];
    
            $NumCtaPago=$cfdiComprobante['NumCtaPago'];
            
            $certificado_key=$cfdiComprobante['certificado'];
            $forma_pago=autoformato_impresion( $cfdiComprobante['formaDePago']);
            $certificado_no=$cfdiComprobante['noCertificado'];
            $cfdiComprobante['tipoDeComprobante'];
            $LugarExpedicion=autoformato_impresion($cfdiComprobante['LugarExpedicion']);
          
            //PARCIALIDADES
            $FechaFolioFiscalOrig=$cfdiComprobante['FechaFolioFiscalOrig'];
            $FolioFiscalOrig=$cfdiComprobante['FolioFiscalOrig'];
            $MontoFolioFiscalOrig=$cfdiComprobante['MontoFolioFiscalOrig'];
            $SerieFolioFiscalOrig=$cfdiComprobante['SerieFolioFiscalOrig'];
            if($FolioFiscalOrig !='')
            {
            $html_parcialidades="
              
                 Folio Fiscal Original: $FolioFiscalOrig | Fecha: $FechaFolioFiscalOrig | Serie Folio: $SerieFolioFiscalOrig | Monto: $MontoFolioFiscalOrig
              ";  
            }
        }
        if($version=="3.3")
        {
            $fecha_expedicion= $cfdiComprobante['Fecha'];
            //TODO: aki etiketa de cuenta en cheke
            $metodo_pago= autoformato_impresion($cfdiComprobante['MetodoPago']);
            $metodo_pago=formato_metodo_pago33($metodo_pago);
            
            $sello= $cfdiComprobante['Sello'];
            $total=$cfdiComprobante['Total'];
            $total_=number_format((string)$total,2);
            $Moneda=$cfdiComprobante['Moneda'];
            
            $subtotal=$cfdiComprobante['SubTotal'];
            $subtotal_=number_format((string)$subtotal,2);
            $TipoDeComprobante = $cfdiComprobante['TipoDeComprobante'];
            $descuento=$cfdiComprobante['Descuento'];
            $descuento_=number_format((string)$descuento,2);
          
            $serie=$cfdiComprobante['Serie'];
            $folio=$cfdiComprobante['Folio'];
    
            $NumCtaPago=$cfdiComprobante['NumCtaPago'];
            
            $certificado_key=$cfdiComprobante['Certificado'];
            $forma_pago=autoformato_impresion( $cfdiComprobante['FormaPago']);
            $forma_pago = formato_forma_pago33($forma_pago);
            $certificado_no=$cfdiComprobante['NoCertificado'];
            $cfdiComprobante['TipoDeComprobante'];
            $LugarExpedicion=autoformato_impresion($cfdiComprobante['LugarExpedicion']);
          
            //PARCIALIDADES
            /*
            $FechaFolioFiscalOrig=$cfdiComprobante['FechaFolioFiscalOrig'];
            $FolioFiscalOrig=$cfdiComprobante['FolioFiscalOrig'];
            $MontoFolioFiscalOrig=$cfdiComprobante['MontoFolioFiscalOrig'];
            $SerieFolioFiscalOrig=$cfdiComprobante['SerieFolioFiscalOrig'];
            if($FolioFiscalOrig !='')
            {
            $html_parcialidades="
              
                 Folio Fiscal Original: $FolioFiscalOrig | Fecha: $FechaFolioFiscalOrig | Serie Folio: $SerieFolioFiscalOrig | Monto: $MontoFolioFiscalOrig
              ";  
            }
            */
        }
        
        
        
    }

    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $Emisor)
    {
        if($version=='3.2')
        {
            $emisor_rfc=$Emisor['rfc'];
            $emisor_nombre= autoformato_impresion($Emisor['nombre']);
        }
        if($version=='3.3')
        {
            $emisor_rfc=$Emisor['Rfc'];
            $emisor_nombre= autoformato_impresion($Emisor['Nombre']);
            $regimen_fiscal=$Emisor['RegimenFiscal'];
        }
    }
    //3.2
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:DomicilioFiscal') as $DomicilioFiscal)
    {
        $emisor_pais= autoformato_impresion($DomicilioFiscal['pais']);
        $emisor_calle= autoformato_impresion($DomicilioFiscal['calle']);
        $emisor_estado= autoformato_impresion($DomicilioFiscal['estado']);
        $emisor_colonia= autoformato_impresion($DomicilioFiscal['colonia']);
        $emisor_municipio= autoformato_impresion($DomicilioFiscal['municipio']);
        $emisor_localidad= autoformato_impresion($DomicilioFiscal['localidad']);
        $emisor_noExterior= autoformato_impresion($DomicilioFiscal['noExterior']);
        $emisor_noInterior= autoformato_impresion($DomicilioFiscal['noInterior']);
        $emisor_CP= autoformato_impresion($DomicilioFiscal['codigoPostal']);
        $emisor_CP=sprintf('%05d',$emisor_CP);
    }
    //3.2
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:ExpedidoEn') as $ExpedidoEn)
    {
        $expedido_pais= autoformato_impresion($ExpedidoEn['pais']);
        $expedido_calle=autoformato_impresion($ExpedidoEn['calle']);
        $expedido_estado=autoformato_impresion($ExpedidoEn['estado']);
        $expedido_colonia=autoformato_impresion($ExpedidoEn['colonia']);
        $expedido_noExterior=autoformato_impresion($ExpedidoEn['noExterior']);
        $expedido_noInterior=autoformato_impresion($ExpedidoEn['noInterior']);
        $expedido_CP=autoformato_impresion($ExpedidoEn['codigoPostal']);
        $expedido_CP=sprintf('%05d',$expedido_CP);
        $expedido_municipio=autoformato_impresion($ExpedidoEn['municipio']);
        $expedido_localidad=autoformato_impresion($ExpedidoEn['localidad']);
    }
    //3.2
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:RegimenFiscal') as $RegimenFiscal)
    {
        if($version=='3.2')
            $regimen_fiscal=autoformato_impresion($RegimenFiscal['Regimen']);
    }
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $Receptor)
    {
        if($version=='3.2'){   
            $receptor_rfc=$Receptor['rfc'];
            $receptor_nombre=autoformato_impresion($Receptor['nombre']);
        }
        if($version=='3.3'){   
            $receptor_rfc=$Receptor['Rfc'];
            $receptor_nombre=autoformato_impresion($Receptor['Nombre']);
            $uso_CFDi=$Receptor['UsoCFDI'];
        }
    }
    //3.2
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor//cfdi:Domicilio') as $ReceptorDomicilio)
    {
        $receptor_pais=autoformato_impresion($ReceptorDomicilio['pais']);
        $receptor_calle=autoformato_impresion($ReceptorDomicilio['calle']);
        $receptor_estado=autoformato_impresion($ReceptorDomicilio['estado']);
        $receptor_colonia=autoformato_impresion($ReceptorDomicilio['colonia']);
        $receptor_municipio=autoformato_impresion($ReceptorDomicilio['municipio']);
        $receptor_localidad=autoformato_impresion($ReceptorDomicilio['localidad']);
        $receptor_noExterior=autoformato_impresion($ReceptorDomicilio['noExterior']);
        $receptor_noInterior=autoformato_impresion($ReceptorDomicilio['noInterior']);
        $receptor_CP=autoformato_impresion($ReceptorDomicilio['codigoPostal']);
        $receptor_CP=sprintf('%05d',$receptor_CP);
    }
    
    /***************************** PRODUCTOS **************************/
    if($version=='3.2'){
        $desgloce='<table width="100%">
           <tr class="factura_detalles_cabecera">
            <td width="44px">CNT</td>
            <td  width="75px">UNIDAD</td>
            <td width="75px">CODIGO</td>
            <td>DESCRIPCION</td>
            <td   width="100px" align="right">PRECIO UNITARIO</td>
            <td   width="100px"  align="right">IMPORTE</td>
           </tr>
        
        ';
    }
    if($version=='3.3'){
        $desgloce='<table width="100%">
           <tr class="factura_detalles_cabecera">
           <td width="44px">CveProdServ</td>
           <td width="44px">NoIdent</td>
           <td width="44px">CNT</td>
            <td width="44px">CveUnidad</td>
            <td  width="75px">UNIDAD</td>
            <td width="75px">DESCRIPCION</td>
            <td   width="100px" align="right">PRECIO UNITARIO</td>
            <td   width="100px"  align="right">IMPORTE</td>
           </tr>
        
        ';
    }
    
    if($TipoDeComprobante=='P'){
        $desgloce='<table width="100%">
           <tr class="factura_detalles_cabecera">
           <td width="44px">CveProdServ</td>
            <td width="44px">CNT</td>
            <td width="44px">CveUnidad</td>
            <td  width="75px">UNIDAD</td>
            <td width="75px">DESCRIPCION</td>
            <td   width="100px" align="right">PRECIO UNITARIO</td>
            <td   width="100px"  align="right">IMPORTE</td>
           </tr>
        
        ';
    }
    $tmp=1;
    
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:CuentaPredial') as $PredialData)
    {
        $predial=(string) $PredialData['numero'];
        if($predial!='')
        {
            $predial="<br/>PREDIAL : $predial";
        }
    }

    $subtotal_productos=0.00;
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto)
    {
        if($version=='3.2')
        {  
            $unidad=$Concepto['unidad'];
            $importe=$Concepto['importe'];
            $cantidad=$Concepto['cantidad'];
            $descripcion=$Concepto['descripcion'];
            $descripcion=str_replace("\n","<br/>",$descripcion);
            //$descripcion=str_replace("\r","<br/>",$descripcion);
            $descripcion=$descripcion.$predial;
            $precio_unitario=$Concepto['valorUnitario'];
            $codigo=$Concepto['noIdentificacion'];
            $numero=$Concepto['numero'];
            if($tmp==0)
            {
                $class='factura_detalles_renglon1';
                $tmp=1;
            }else{    
                $class='factura_detalles_renglon2';
                $tmp=0;
            }
            $descripcion=autoformato_impresion($descripcion);
            $precio_unitario_=number_format((string)$precio_unitario,2);     
            $importe_=number_format((string)$importe,2);  
            $subtotal_productos+=(float)$importe;
            $desgloce.="
                <tr class='$class'>
                <td>$cantidad</td>
                <td>$unidad </td>
                <td>$codigo </td>
                <td>$descripcion</td>
                <td align='right'>$$precio_unitario_</td>
                <td  align='right'>$$importe_</td>
                </tr>
                ";
        }
        if($version=='3.3')
        {  
            $CveProdServ=$Concepto['ClaveProdServ'];
            $CveUnidad=$Concepto['ClaveUnidad'];
            
            
            $unidad=$Concepto['Unidad'];
            $importe=$Concepto['Importe'];
            $cantidad=$Concepto['Cantidad'];
            $descripcion=$Concepto['Descripcion'];
            $descripcion=str_replace("\n","<br/>",$descripcion);
            //$descripcion=str_replace("\r","<br/>",$descripcion);
            $descripcion=$descripcion.$predial;
            $precio_unitario=$Concepto['ValorUnitario'];
            $codigo=$Concepto['NoIdentificacion'];
            $numero=$Concepto['numero'];
            if($tmp==0)
            {
                $class='factura_detalles_renglon1';
                $tmp=1;
            }else{    
                $class='factura_detalles_renglon2';
                $tmp=0;
            }
            $descripcion=autoformato_impresion($descripcion);
            $precio_unitario_=number_format((string)$precio_unitario,2);     
            $importe_=number_format((string)$importe,2);  
            $subtotal_productos+=(float)$importe;
            /*
            $desgloce.="
                <tr class='$class'>
                <td>$CveProdServ</td>
                <td>$codigo</td>
                <td>$cantidad</td>
                <td>$CveUnidad </td>
                <td>$unidad </td>
                <td>$descripcion</td>
                <td align='right'>$$precio_unitario_</td>
                <td  align='right'>$$importe_</td>
                </tr>
                ";
             */   
            if($TipoDeComprobante != 'P')
            {
                $desgloce.="
                <tr class='$class'>
                <td>$CveProdServ</td>
                <td>$codigo</td>
                <td>$cantidad</td>
                <td>$CveUnidad </td>
                <td>$unidad </td>
                <td>$descripcion</td>
                <td align='right'>$$precio_unitario_</td>
                <td  align='right'>$$importe_</td>
                </tr>
                ";    
            }else{  //ES UN PAGO
                $desgloce.="
                <tr class='$class'>
                <td>$CveProdServ</td>
                <td>$cantidad</td>
                <td>$CveUnidad </td>
                <td>$unidad </td>
                <td>$descripcion</td>
                <td align='right'>$$precio_unitario_</td>
                <td  align='right'>$$importe_</td>
                </tr>
                ";
            }    
        }
    }
    $desgloce.='</table>';
    
$isr_retenido=0.00;
$iva_retenido=0.00;

    foreach ($xml->xpath('//tfd:TimbreFiscalDigital') as $tfd)
    {
        if($version=='3.2')
        {
            $timbre_selloCFD= $tfd['selloCFD'];
            $timbre_fecha= $tfd['FechaTimbrado'];
            $timbre_uuid= $uuid=$tfd['UUID'];
            $timbre_noCertificadoSAT= $tfd['noCertificadoSAT'];
            $timbre_version= $tfd['version'];
            $timbre_selloSAT = $sellosat=$tfd['selloSAT'];
        }
        if($version=='3.3')
        {
            $timbre_selloCFD= $tfd['SelloCFD'];
            $timbre_fecha= $tfd['FechaTimbrado'];
            $timbre_uuid= $uuid=$tfd['UUID'];
            $timbre_noCertificadoSAT= $tfd['NoCertificadoSAT'];
            $timbre_version= $tfd['Version'];
            $timbre_selloSAT = $sellosat=$tfd['SelloSAT'];
        }   
    }
    
    //TRANSLADOS (impuestos comprobante)
    $total_translados=$total_translados_locales=0;
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado') as $Traslado)
    {
        if($version=='3.2')
        {
            $tasa=$Traslado['tasa'];
            $importe=$Traslado['importe'];
            $importe_=number_format((string)$importe,2);
            $impuesto= $Traslado['impuesto'];
            $total_translados=$total_translados+(float)$importe;
            $iva_txt.="
                    <tr>
                        <td class='factura_totales'>
                        $impuesto ($tasa%)
                        </td>
                        <td class='factura_totales'>
                         $importe_ 
                        </td>
                    </tr>
            ";
        }
        if($version=='3.3')
        {
            $Base=$Traslado['Base'];         //COMPARA SI ES IMPUESTO DE PRODUCTO O DE COMPROBANTE
            $tasa=$Traslado['TasaOCuota'];
            $importe=$Traslado['Importe'];
            $importe_=number_format((string)$importe,2);
            $impuesto= $Traslado['Impuesto'];
            $impuesto_txt=formato_impuestos($impuesto);
            $TipoFactor= $Traslado['TipoFactor'];
            if($Base =='')
            {
                $total_translados=$total_translados+(float)$importe;
                $iva_txt.="
                        <tr>
                            <td class='factura_totales'>
                            $impuesto_txt ($tasa%)  $Base
                            </td>
                            <td class='factura_totales'>
                             $importe_ 
                            </td>
                        </tr>
                ";
            }
        }
    }

    //LOCALES 
    $cadena=file_get_contents($xml_archivo);
    if(strpos($cadena,'ImpuestosLocales')>0)
    {
        list($tmp,$cadena,$tmp)=explode('ImpuestosLocales',$cadena);
        list($tmp,$cadena2)=explode('>',(string)$cadena,2);//
        $cadena="<implocal:ImpuestosLocales  >
            $cadena2"."ImpuestosLocales>
            ";
        $xml2 = simplexml_load_string($cadena);
        $arr = object2array($xml2);
        $TrasladosLocales=$arr['TrasladosLocales'];
        
        //TRANSLADO LOCAL
        foreach($TrasladosLocales AS $llave_=>$TrasladosLocal)
        {
            $ImpLocTrasladado=$TrasladosLocal['@attributes']['ImpLocTrasladado'];
            if($ImpLocTrasladado=='')
                $ImpLocTrasladado=$TrasladosLocal['ImpLocTrasladado'];
    
            $Importe=$TrasladosLocal['@attributes']['Importe'];
            if($Importe=='')
                $Importe=$TrasladosLocal['Importe']; 
    
            $TasadeTraslado=$TrasladosLocal['@attributes']['TasadeTraslado'];
            if($TasadeTraslado=='')
                $TasadeTraslado=$TrasladosLocal['TasadeTraslado'];
            
            $total_translados=$total_translados+(float)$Importe;
    
            //echo "TL $ImpLocTrasladado $TasadeTraslado% $Importe<br>";
            $importe_=number_format((string)$Importe,2);
            $iva_txt.="
                    <tr>
    
                        <td class='factura_totales'>
                        (LOCAL) $ImpLocTrasladado ($TasadeTraslado%) 
                        </td>
                        <td class='factura_totales'>
                         $importe_
                        </td>
                    </tr>
            ";        
        }
    
        //RETENCIONES LOCAL
        $RetencionesLocales=$arr['RetencionesLocales'];
        foreach($RetencionesLocales AS $llave_=>$RetencionesLocal)
        {
            $ImpLocRetenido=$RetencionesLocal['@attributes']['ImpLocRetenido'];
            if($ImpLocRetenido=='')
                $ImpLocRetenido=$RetencionesLocal['ImpLocRetenido'];
    
            $Importe=$RetencionesLocal['@attributes']['Importe'];
            if($Importe=='')
                $Importe=$RetencionesLocal['Importe']; 
    
            $TasadeRetencion=$RetencionesLocal['@attributes']['TasadeRetencion'];
            if($TasadeRetencion=='')
                $TasadeRetencion=$RetencionesLocal['TasadeRetencion'];
    
            $importe_=number_format((string)$Importe,2);
            $importe_retenciones=$importe_retenciones+$Importe;
            
            $retenciones_txt.="
                           <tr>
                                <td class='factura_totales'>
                                RET LOCAL $ImpLocRetenido ($TasadeRetencion%) $
                                </td>
                                <td class='factura_totales'>
                                 $importe_
                                </td>
                            </tr>
    
           ";                    
        }
    }
        


//RETENCIONES
//    $retenciones_txt='';
//    $importe_retenciones=0.00;
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Retenciones//cfdi:Retencion') as $Retencion)
    {
       if($version=='3.2')
       {
           $importe=$Retencion['importe'];
           $impuesto=$Retencion['impuesto'];
           $importe_retenciones=$importe_retenciones+(float)$importe;
           $importe_=number_format((string)$importe,2);
           $retenciones_txt.="
                           <tr>
                                <td class='factura_totales'>
                                RET $impuesto $
                                </td>
                                <td class='factura_totales'>
                                 $importe_
                                </td>
                            </tr>
            ";
        }
        if($version=='3.3')
        {
            $Base=$Retencion['Base'];
            $importe=$Retencion['Importe'];
            $impuesto=$Retencion['Impuesto'];
            $importe_retenciones=$importe_retenciones+(float)$importe;
            $importe_=number_format((string)$importe,2);
            $impuesto_txt_ret=formato_impuestos($impuesto);
            if($Base =='')
            {
                $retenciones_txt.="
                        <tr>
                            <td class='factura_totales'>
                            RET $impuesto_txt_ret $
                            </td>
                            <td class='factura_totales'>
                             $importe_
                            </td>
                        </tr>
                ";
            }
        }
    }
    
    if($importe_retenciones==0)
    {
       $retenciones_txt=''; 
    }


    // SI HAY RETENCIONES MUESTA EL SUBTOTAL ANTES DE RETENCIONES CON IMPUESTOS AGREGADOS
    if($retenciones_txt!='')
    {
        $subtotal_con_retenciones=(float)$subtotal+(float)$total_translados+(float)$total_translados_locales;
        $subtotal_con_retenciones=number_format($subtotal_con_retenciones-$descuento_,2);
        //$subtotal_ //aki
        $retenciones_txt="
                       <tr>
                            <td class='factura_totales'>
                            SUB TOTAL $
                            </td>
                            <td class='factura_totales'>
                            
                             $subtotal_con_retenciones
                            </td>
                        </tr>
                        $retenciones_txt
        ";
    }

    //INE
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Complemento//ine:INE') as $INE)
    {
        $TipoProceso=$INE['TipoProceso'];
        $TipoComite=$INE['TipoComite'];
        $IdContabilidad=$DescInmueble['IdContabilidad'];
        
        $html_Ine.= "<hr/><div>
                                INE:<br/><br/>
                                Tipo de Proceso: $TipoProceso Comite: $TipoComite Contabilidad $IdContabilidad<br/>
                                Col.: $Colonia Localidad: $Localidad<br/>
                                Estado: $Estado Pais: $Pais<br/>
                                C.P.: $CodigoPostal
                                </div>
                                ";
    }
    //
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Complemento//ine:INE//ine:Entidad') as $Entidad)
    {
        $ClaveEntidad=$Entidad['ClaveEntidad'];
        $Ambito=$Entidad['Ambito'];
        $TipoComite=$Entidad['TipoComite'];
        
        $html_Entidad.= "<hr/>
                        <div>
                            Clave Entidad: $ClaveEntidad Ambito: $Ambito Tipo Comite $TipoComite<br/>
                        </div>
                                ";
        
        foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Complemento//ine:INE//ine:Entidad//ine:Contabilidad') as $Entidad_Contabilidad)
        {
            $IdContabilidad=$Entidad_Contabilidad['IdContabilidad'];
            
            $html_Entidad.= "
                            <div>
                                Contabilidad: $IdContabilidad<br/>
                            </div>
                                    ";
            }
    }
    
    if($TipoProceso!=''  OR $TipoProceso !='no_proceso')
    {
    $INE_general="
            <div>
            <table width='70%' border=0  >
                <tr><td>$html_Ine</td></tr>
                <tr><td>$html_Entidad</td></tr>
            </table>
            </div>
            ";
    }   


////////
//PAGOS
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Complemento//pago10:Pagos') as $Pagos)
    {
    
    }
    //$HTML_PAGO="<table width='100%'>
    //<tr class='factura_detalles_cabecera'><td width='33%'>Fecha de pago</td><td width='33%'>Forma de Pago</td><td width='33%'>Total</td></tr>";
    $P=0;
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Complemento//pago10:Pagos//pago10:Pago') as $Pago)
    {
       $P++;
       $HTML_PAGO="<table width='100%'>
                            <tr class='factura_detalles_cabecera'><td colspan='3'>PAGO</td></tr>"; 
        
        $FechaPago=$Pago['FechaPago'];
        $FormaDePagoP=$Pago['FormaDePagoP'];
        $MonedaP=$Pago['MonedaP'];
        $TipoCambioP=$Pago['TipoCambioP'];
        $Monto_Pago=$Pago['Monto'];
        $NumOperacion=$Pago['NumOperacion'];
        if($NumOperacion !='')
            $NumOperacion_txt="Num. operacion: $NumOperacion <br/>";
            
        $RfcEmisorCtaOrd=$Pago['RfcEmisorCtaOrd'];
        if($RfcEmisorCtaOrd !='')
            $RfcEmisorCtaOrd_txt="RFC Emisor cuenta: $RfcEmisorCtaOrd <br/>";
            
        $NomBancoOrdExt=$Pago['NomBancoOrdExt'];
        if($NomBancoOrdExt !='')
            $NomBancoOrdExt_txt="Banco: $NomBancoOrdExt ";  
        
        $CtaOrdenante=$Pago['CtaOrdenante'];
        if($CtaOrdenante !='')
            $CtaOrdenante_txt="Num. Cuenta Ordenante: $CtaOrdenante <br/>";  
            
        $RfcEmisorCtaBen=$Pago['RfcEmisorCtaBen'];
        if($RfcEmisorCtaBen !='')
            $RfcEmisorCtaBen_txt="RFC Cuenta Beneficiario: $RfcEmisorCtaBen <br/>";
            
        $CtaBeneficiario=$Pago['CtaBeneficiario'];
        if($CtaBeneficiario !='')
            $CtaBeneficiario_txt="Num Cuenta Beneficiario: $CtaBeneficiario";
        
        $FormaDePagoP_txt=formato_forma_pago33($FormaDePagoP);
        $Monto_Pago_txt=number_format((string)$Monto_Pago,2);
        
        $TipoCadPago=$Pago['TipoCadPago'];
        $CertPago=$Pago['CertPago'];
        $CadPago=$Pago['CadPago'];
        $SelloPago=$Pago['SelloPago'];
        if($TipoCadPago !='')
        {
            $tr_="<tr><td colspan='3'>
                    Cadena pago $TipoCadPago <br/>
                    Certificado pago $CertPago <br/>
                    Cadena origianal pago $CertPago <br/>
                    Sello pago $SelloPago <br/>
                    </td></tr>";
        }
        
        $HTML_PAGO.="<tr>
                        <td width='33%'>Monto: $Monto_Pago_txt <br/> $NumOperacion_txt Fecha de pago: $FechaPago <br/> Forma de pago: $FormaDePagoP_txt</td>
                        <td width='33%'>$RfcEmisorCtaOrd_txt $NomBancoOrdExt_txt </td></tr>
                        <td width='33%'>$CtaOrdenante_txt $RfcEmisorCtaBen_txt  $CtaBeneficiario_txt</td>
                        $tr_
                    </tr>
                    ";
        $HTML_PAGO.="</table>";
        
        
        $PAGOS__[$P]=$HTML_PAGO;
    }
    //$HTML_."PAGO_".$P.="</table>";
    
    
    $D=0;
    //$HTML_DOCUMENTOS_PAGOS="<table width='100%'>
    //<tr class='factura_detalles_cabecera'><td>UUID</td><td>Metodo de pago</td><td>Saldo anterior</td><td>Monto Pagado</td><td>Saldo Pendiente</td></tr>";
    foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Complemento//pago10:Pagos//pago10:Pago//pago10:DoctoRelacionado') as $DoctoRelacionado)
    {
        $D++;
        $HTML_DOCUMENTOS_PAGOS="<table width='100%'>
    <tr class='factura_detalles_cabecera'><td>UUID</td><td>Metodo de pago</td><td>Saldo anterior</td><td>Monto Pagado</td><td>Saldo Pendiente</td></tr>";
        
        $IdDocumento=$DoctoRelacionado['IdDocumento'];
        $MonedaDR=$DoctoRelacionado['MonedaDR'];
        $TipoCambioDR=$DoctoRelacionado['TipoCambioDR'];
        $MetodoDePagoDR=$DoctoRelacionado['MetodoDePagoDR'];
        $NumParcialidad=$DoctoRelacionado['NumParcialidad'];
        $ImpSaldoAnt=$DoctoRelacionado['ImpSaldoAnt'];
        $ImpPagado=$DoctoRelacionado['ImpPagado'];
        $ImpSaldoInsoluto=$DoctoRelacionado['ImpSaldoInsoluto'];
        $MetodoDePagoDR_txt=formato_metodo_pago33($MetodoDePagoDR);
        
        $ImpSaldoAnt_txt=number_format((string)$ImpSaldoAnt,2);
        $ImpPagado_txt=number_format((string)$ImpPagado,2);
        $ImpSaldoInsoluto_txt=number_format((string)$ImpSaldoInsoluto,2);
        
        $HTML_DOCUMENTOS_PAGOS.="
        <tr><td>$IdDocumento</td><td>$MetodoDePagoDR_txt</td><td>$ImpSaldoAnt_txt</td><td>$ImpPagado_txt</td><td>$ImpSaldoInsoluto_txt</td></tr>";
        
        $HTML_DOCUMENTOS_PAGOS.="</table>";
        
        
        $DOC__[$D]=$HTML_DOCUMENTOS_PAGOS;
       
    }

    $CANT_PAGOS=count($PAGOS__);
    for($b=0;$b<=$CANT_PAGOS;$b++)
    {
       $pago_html= $PAGOS__[$b];
       $doc_html = $DOC__[$b];
        $HTML_PAGOS.="
       $pago_html
       $doc_html
       <hr/>
       ";
    }
    if($TipoDeComprobante !='P')
    {
        $HTML_PAGOS="";   
    }
    
    
//////////////
//NOMINA 1.1
if(array_key_exists('nomina', $ns))
{
    foreach ($xml->xpath('//nomina:Nomina') as $Nomina)
    {
        $RegistroPatronal= autoformato_impresion($Nomina['RegistroPatronal']);
        $NumEmpleado= autoformato_impresion($Nomina['NumEmpleado']);
        $CURP= autoformato_impresion($Nomina['CURP']);
        
        $TipoRegimen= autoformato_impresion($Nomina['TipoRegimen']);
        
        $NumSeguridadSocial= autoformato_impresion($Nomina['NumSeguridadSocial']);
        $FechaPago= autoformato_impresion($Nomina['FechaPago']);
        $FechaInicialPago= autoformato_impresion($Nomina['FechaInicialPago']);
        $FechaFinalPago= autoformato_impresion($Nomina['FechaFinalPago']);
        $NumDiasPagados= autoformato_impresion($Nomina['NumDiasPagados']);
        $Departamento= autoformato_impresion($Nomina['Departamento']);
        $Banco= autoformato_impresion($Nomina['Banco']);
        $CLABE= autoformato_impresion($Nomina['CLABE']);
        $FechaInicioRelLaboral= autoformato_impresion($Nomina['FechaInicioRelLaboral']);
        $Antiguedad= autoformato_impresion($Nomina['Antiguedad']);
        $Puesto= autoformato_impresion($Nomina['Puesto']);
        $TipoContrato= autoformato_impresion($Nomina['TipoContrato']);
        $TipoJornada= autoformato_impresion($Nomina['TipoJornada']);
        $PeriodicidadPago= autoformato_impresion($Nomina['PeriodicidadPago']);
        $SalarioBaseCotApor= autoformato_impresion($Nomina['SalarioBaseCotApor']);
        $RiesgoPuesto= autoformato_impresion($Nomina['RiesgoPuesto']);
        $SalarioDiarioIntegrado= autoformato_impresion($Nomina['SalarioDiarioIntegrado']);
        $RegistroPatronal= autoformato_impresion($Nomina['RegistroPatronal']);
       
    }
    if($CURP!='')
    {
        $letra=10;
        $nomina_general="
        <div>
        <table width='100%' border=0 class='factura_titulo_ch' >
            <tr>
            <td width='50%' style='font-size:$letra px !important'> 
            Registro Patronal : $RegistroPatronal</td><td style='font-size:$letra px !important'> Fecha Inicio Laboral : $FechaInicioRelLaboral
            </td></tr><tr><td style='font-size:$letra px !important'> Tipo de Regimen : $TipoRegimen</td><td style='font-size:$letra px !important'> Fecha Inicial : $FechaInicialPago
            </td></tr><tr><td style='font-size:$letra px !important'> Numero de Empleado : $NumEmpleado</td><td style='font-size:$letra px !important'> Fecha Final : $FechaFinalPago
            </td></tr><tr><td style='font-size:$letra px !important'> CURP : $CURP</td><td style='font-size:$letra px !important'> Fecha de Pago : $FechaPago
            </td></tr><tr><td style='font-size:$letra px !important'> No. Seguro Social : $NumSeguridadSocial</td><td style='font-size:$letra px !important'> Dias pagados : $NumDiasPagados
            </td></tr><tr><td style='font-size:$letra px !important'> DEPARTAMENTO : $Departamento</td><td style='font-size:$letra px !important'> Antiguedad : $Antiguedad semanas
            </td></tr><tr><td style='font-size:$letra px !important'> PUESTO : $Puesto</td><td style='font-size:$letra px !important'> PERIODICIDAD : $PeriodicidadPago
            </td></tr><tr><td style='font-size:$letra px !important'> BANCO :$Banco</td><td style='font-size:$letra px !important'> SALARIO BASE : $SalarioBaseCotApor
            </td></tr><tr><td style='font-size:$letra px !important'> CLAVE : $CLABE</td><td style='font-size:$letra px !important'> RIESGO : $RiesgoPuesto
            </td></tr><tr><td style='font-size:$letra px !important'> TIPO CONTRATO : $TipoContrato</td><td style='font-size:$letra px !important'> SALARIO DIARIO INTEGRADO: $SalarioDiarioIntegrado
            </td></tr><tr><td style='font-size:$letra px !important'> TIPO JORNADA : $TipoJornada</td><td>
    
            </td></tr>
        </table>
        </div>    
        ";
    }

//NOMINAS PERCEPCIONES
    $NominaPercepciones='';
    foreach ($xml->xpath('//nomina:Percepcion') as $Percepciones)
    {
        
        $TipoPercepcion= autoformato_impresion($Percepciones['TipoPercepcion']);
        $Clave= autoformato_impresion($Percepciones['Clave']);
        $Concepto= autoformato_impresion($Percepciones['Concepto']);
        $ImporteGravado= autoformato_impresion($Percepciones['ImporteGravado']);
        $ImporteExento= autoformato_impresion($Percepciones['ImporteExento']);
        
        
        $NominaPercepciones.="<tr><td style='font-size:$letra px !important'>$TipoPercepcion</td><td style='font-size:$letra px !important'>$Clave</td><td style='font-size:$letra px !important'>$Concepto</td><td style='font-size:$letra px !important'>$ImporteGravado</td><td style='font-size:$letra px !important'>$ImporteExento</td></tr>";
        

    }
    if(strlen($NominaPercepciones)>10)
    {
        $NominaPercepciones="
        <b>PERCEPCIONES:</b>
            <table width='100%'>
       <tr class='factura_detalles_cabecera'  >
        <td style='font-size:$letra px !important'>TP</td>
        <td style='font-size:$letra px !important'>CLAVE</td>
        <td style='font-size:$letra px !important' >CONCEPTO</td>
        <td style='font-size:$letra px !important'>IMP GRAVADO</td>
        <td style='font-size:$letra px !important'>IMP EXCENTO</td>
       </tr>

            $NominaPercepciones
            </table>
        ";
    }


//NOMINAS DEDUCCIONES
    $NominaDeducciones='';
    foreach ($xml->xpath('//nomina:Deduccion') AS $Deducciones)
    {
        
        $TipoDeduccion= autoformato_impresion($Deducciones['TipoDeduccion']);
        $Clave= autoformato_impresion($Deducciones['Clave']);
        $Concepto= autoformato_impresion($Deducciones['Concepto']);
        $ImporteGravado= autoformato_impresion($Deducciones['ImporteGravado']);
        $ImporteExento= autoformato_impresion($Deducciones['ImporteExento']);
        
        $NominaDeducciones.="<tr><td style='font-size:$letra px !important'>$TipoDeduccion</td><td style='font-size:$letra px !important'>$Clave</td><td style='font-size:$letra px !important'>$Concepto</td><td style='font-size:$letra px !important'>$ImporteGravado</td><td style='font-size:$letra px !important'>$ImporteExento</td></tr>";
        

    }
    if(strlen($NominaDeducciones)>10)
    {
        $NominaDeducciones="
        <b>DEDUCCIONES:</b>
            <table width='100%'>
       <tr class='factura_detalles_cabecera'  >
        <td style='font-size:$letra px !important'>TP</td>
        <td style='font-size:$letra px !important'>CLAVE</td>
        <td style='font-size:$letra px !important'>CONCEPTO</td>
        <td style='font-size:$letra px !important'>IMP GRAVADO</td>
        <td style='font-size:$letra px !important'>IMP EXCENTO</td>
       </tr>

            $NominaDeducciones
            </table>
        ";
    }



//NOMINAS HORAS EXTRA
    $NominaHorasExtras='';
    foreach ($xml->xpath('//nomina:HorasExtra') AS $HoraExtra)
    {
        
        $Dias= autoformato_impresion($HoraExtra['Dias']);
        $TipoHoras= autoformato_impresion($HoraExtra['TipoHoras']);
        $HorasExtra= autoformato_impresion($HoraExtra['HorasExtra']);
        
        $NominaHorasExtras.="<tr><td style='font-size:$letra px !important'>$Dias</td><td style='font-size:$letra px !important'>$TipoHoras</td><td style='font-size:$letra px !important'>$HorasExtra</td></tr>";

        

    }
    if(strlen($NominaHorasExtras)>10)
    {
        $NominaHorasExtras="
        <b>HORAS EXTRA:</b>
            <table width='100%'>
       <tr class='factura_detalles_cabecera'  >
        <td style='font-size:$letra px !important'>DIAS</td>
        <td style='font-size:$letra px !important'>TIPO</td>
        <td style='font-size:$letra px !important'>HORAS EXTRA</td>
       </tr>

            $NominaHorasExtras
            </table>
        ";
    }



//NOMINAS INCAPACIDADES
    $NominaIncapacidades='';
    foreach ($xml->xpath('//nomina:Incapacidad') AS $Incapacidad)
    {
        
        $DiasIncapacidad= autoformato_impresion($Incapacidad['DiasIncapacidad']);
        $TipoIncapacidad= autoformato_impresion($Incapacidad['TipoIncapacidad']);
        $Descuento= autoformato_impresion($Incapacidad['Descuento']);
        
        $NominaIncapacidades.="<tr><td style='font-size:$letra px !important'>$DiasIncapacidad</td><td style='font-size:$letra px !important'>$TipoIncapacidad</td><td style='font-size:$letra px !important'>$Descuento</td></tr>";

        

    }
    if(strlen($NominaIncapacidades)>10)
    {
        $NominaIncapacidades="
        <b>INCAPACIDADES:</b>
            <table width='100%'>
       <tr class='factura_detalles_cabecera'  >
        <td style='font-size:$letra px !important'>DIAS</td>
        <td style='font-size:$letra px !important'>TIPO</td>
        <td style='font-size:$letra px !important'>DESCUENTO</td>
       </tr>

            $NominaIncapacidades
            </table>
        ";
    }

}

// NOMINA 1.2
if(array_key_exists('nomina12', $ns))
{
	// Catalagos
	$catEntidad = array('AGU' => 'Aguascalientes', 'BCN' => 'Baja California', 'BCS' => 'Baja California Sur', 'CAM' => 'Campeche', 'CHP' => 'Chiapas', 'CHH' => 'Chihuahua', 'COA' => 'Coahuila', 'COL' => 'Colima', 'DIF' => 'Ciudad de México', 'DUR' => 'Durango', 'GUA' => 'Guanajuato', 'GRO' => 'Guerrero', 'HID' => 'Hidalgo', 'JAL' => 'Jalisco', 'MEX' => 'Estado de México', 'MIC' => 'Michoacán', 'MOR' => 'Morelos', 'NAY' => 'Nayarit', 'NLE' => 'Nuevo León', 'OAX' => 'Oaxaca', 'PUE' => 'Puebla', 'QUE' => 'Querétaro', 'ROO' => 'Quintana Roo', 'SLP' => 'San Luis Potosí', 'SIN' => 'Sinaloa', 'SON' => 'Sonora', 'TAB' => 'Tabasco', 'TAM' => 'Tamaulipas', 'TLA' => 'Tlaxcala', 'VER' => 'Veracruz', 'YUC' => 'Yucatán', 'ZAC' => 'Zacatecas', 'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California', 'NC' => 'Carolina del Norte', 'SC' => 'Carolina del Sur', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'ND' => 'Dakota del Norte', 'SD' => 'Dakota del Sur', 'DE' => 'Delaware', 'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawái', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Luisiana', 'ME' => 'Maine', 'MD' => 'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Míchigan', 'MN' => 'Minnesota', 'MS' => 'Misisipi', 'MO' => 'Misuri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NJ' => 'Nueva Jersey', 'NY' => 'Nueva York', 'NH' => 'Nuevo Hampshire', 'NM' => 'Nuevo México', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregón', 'PA' => 'Pensilvania', 'RI' => 'Rhode Island', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont', 'VA' => 'Virginia', 'WV' => 'Virginia Occidental', 'WA' => 'Washington', 'WI' => 'Wisconsin', 'WY' => 'Wyoming', 'ON' => 'Ontario ', 'QC' => ' Quebec ', 'NS' => ' Nueva Escocia', 'NB' => 'Nuevo Brunswick ', 'MB' => ' Manitoba', 'BC' => ' Columbia Británica', 'PE' => ' Isla del Príncipe Eduardo', 'SK' => ' Saskatchewan', 'AB' => ' Alberta', 'NL' => ' Terranova y Labrador', 'NT' => ' Territorios del Noroeste', 'YT' => ' Yukón', 'UN' => ' Nunavut');
	$catOrigenRegusro = array('IP' => 'Ingresos Personales', 'IF' => 'Ingresos Federales', 'IM' => 'Ingresos Mixtos');
	$catTipoHoras = array('01' => 'Dobles', '02' => 'Triples', '03' => 'Simples');
	$catTipoIncapacidad = array('01' => 'Riesgo de trabajo', '02' => 'Enfermedad en general', '03' => 'Maternidad');
	$catTipoOtroPago = array('001' => 'Reintegro de ISR pagado en exceso', '002' => 'Subsidio para el empleo', '003' => 'Viáticos', '004' => 'Aplicación de saldo a favor por compensación anual', '999' => 'Pagos distintos a los listados y que no deben considerarse como ingreso por sueldos, salarios o ingresos asimilados');
	$catTipoContrato = array('01' => 'Contrato de trabajo por tiempo indeterminado', '02' => 'Contrato de trabajo para obra determinada', '03' => 'Contrato de trabajo por tiempo determinado', '04' => 'Contrato de trabajo por temporada', '05' => 'Contrato de trabajo sujeto a prueba', '06' => 'Contrato de trabajo con capacitación inicial', '07' => 'Modalidad de contratación por pago de hora laborada', '08' => 'Modalidad de trabajo por comisión laboral', '09' => 'Modalidades de contratación donde no existe relación de trabajo', '10' => 'Jubilación, pensión, retiro.', '99' => 'Otro contrato');
	$catTipoJornada = array('01' => 'Diurna', '02' => 'Nocturna', '03' => 'Mixta', '04' => 'Por hora', '05' => 'Reducida', '06' => 'Continuada', '07' => 'Partida', '08' => 'Por turnos', '99' => 'Otra Jornada');
	$catTipoRegimen = array ('02' => 'Sueldos', '03' => 'Jubilados', '04' => 'Pensionados', '05' => 'Asimilados Miembros Sociedades Cooperativas Produccion', '06' => 'Asimilados Integrantes Sociedades Asociaciones Civiles', '07' => 'Asimilados Miembros consejos', '08' => 'Asimilados comisionistas', '09' => 'Asimilados Honorarios', '10' => 'Asimilados acciones', '11' => 'Asimilados otros', '99' => 'Otro Regimen');
	$catRiesgoPuesto = array('1' => 'Clase I' , '2' => 'Clase II' , '3' => 'Clase III' , '4' => 'Clase IV' , '5' => 'Clase V');
	$catTipoRegimen = array('02' => 'Sueldos' , '03' => 'Jubilados' , '04' => 'Pensionados' , '05' => 'Asimilados Miembros Sociedades Cooperativas Produccion' , '06' => 'Asimilados Integrantes Sociedades Asociaciones Civiles' , '07' => 'Asimilados Miembros consejos' , '08' => 'Asimilados comisionistas' , '09' => 'Asimilados Honorarios' , '10' => 'Asimilados acciones' , '11' => 'Asimilados otros' , '99' => 'Otro Regimen');
	$catPeriodicidadPago = array('01' => 'Diario' , '02' => 'Semanal' , '03' => 'Catorcenal' , '04' => 'Quincenal' , '05' => 'Mensual' , '06' => 'Bimestral' , '07' => 'Unidad obra' , '08' => 'Comisión' , '09' => 'Precio alzado' , '99' => 'Otra Periodicidad');
	$catBanco = array('002' => 'BANAMEX' , '006' => 'BANCOMEXT' , '009' => 'BANOBRAS' , '012' => 'BBVA BANCOMER' , '014' => 'SANTANDER' , '019' => 'BANJERCITO' , '021' => 'HSBC' , '030' => 'BAJIO' , '032' => 'IXE' , '036' => 'INBURSA' , '037' => 'INTERACCIONES' , '042' => 'MIFEL' , '044' => 'SCOTIABANK' , '058' => 'BANREGIO' , '059' => 'INVEX' , '060' => 'BANSI' , '062' => 'AFIRME' , '072' => 'BANORTE' , '102' => 'THE ROYAL BANK' , '103' => 'AMERICAN EXPRESS' , '106' => 'BAMSA' , '108' => 'TOKYO' , '110' => 'JP MORGAN' , '112' => 'BMONEX' , '113' => 'VE POR MAS' , '116' => 'ING' , '124' => 'DEUTSCHE' , '126' => 'CREDIT SUISSE' , '127' => 'AZTECA' , '128' => 'AUTOFIN' , '129' => 'BARCLAYS' , '130' => 'COMPARTAMOS' , '131' => 'BANCO FAMSA' , '132' => 'BMULTIVA' , '133' => 'ACTINVER' , '134' => 'WAL-MART' , '135' => 'NAFIN' , '136' => 'INTERBANCO' , '137' => 'BANCOPPEL' , '138' => 'ABC CAPITAL' , '139' => 'UBS BANK' , '140' => 'CONSUBANCO' , '141' => 'VOLKSWAGEN' , '143' => 'CIBANCO' , '145' => 'BBASE' , '166' => 'BANSEFI' , '168' => 'HIPOTECARIA FEDERAL' , '600' => 'MONEXCB' , '601' => 'GBM' , '602' => 'MASARI' , '605' => 'VALUE' , '606' => 'ESTRUCTURADORES' , '607' => 'TIBER' , '608' => 'VECTOR' , '610' => 'B&B' , '614' => 'ACCIVAL' , '615' => 'MERRILL LYNCH' , '616' => 'FINAMEX' , '617' => 'VALMEX' , '618' => 'UNICA' , '619' => 'MAPFRE' , '620' => 'PROFUTURO' , '621' => 'CB ACTINVER' , '622' => 'OACTIN' , '623' => 'SKANDIA' , '626' => 'CBDEUTSCHE' , '627' => 'ZURICH' , '628' => 'ZURICHVI' , '629' => 'SU CASITA' , '630' => 'CB INTERCAM' , '631' => 'CI BOLSA' , '632' => 'BULLTICK CB' , '633' => 'STERLING' , '634' => 'FINCOMUN' , '636' => 'HDI SEGUROS' , '637' => 'ORDER' , '638' => 'AKALA' , '640' => 'CB JPMORGAN' , '642' => 'REFORMA' , '646' => 'STP' , '647' => 'TELECOMM' , '648' => 'EVERCORE' , '649' => 'SKANDIA' , '651' => 'SEGMTY' , '652' => 'ASEA' , '653' => 'KUSPIT' , '655' => 'SOFIEXPRESS' , '656' => 'UNAGRA' , '659' => 'OPCIONES EMPRESARIALES DEL NOROESTE' , '901' => 'CLS' , '902' => 'INDEVAL' , '670' => 'LIBERTAD');
	
	// NOMINA
	foreach ($xml->xpath('//nomina12:Nomina') as $Nomina)
    {
		$TipoNomina = autoformato_impresion($Nomina['TipoNomina']);
		$FechaPago= autoformato_impresion($Nomina['FechaPago']);
		$FechaInicialPago= autoformato_impresion($Nomina['FechaInicialPago']);
        $FechaFinalPago= autoformato_impresion($Nomina['FechaFinalPago']);
        $NumDiasPagados= autoformato_impresion($Nomina['NumDiasPagados']);
        $TotalPercepciones = autoformato_impresion($Nomina['TotalPercepciones']);
        $TotalDeducciones = autoformato_impresion($Nomina['TotalDeducciones']);
        $TotalOtrosPagos = autoformato_impresion($Nomina['TotalOtrosPagos']);
		
		//========================
       
    }
        $letra=10;
        $nomina_general="
        <div>
	<table width='100%' border=0 class='factura_titulo_ch' >
		<tr>
			<td width='50%' style='font-size:$letra px !important'> Tipo de Nomina : $TipoNomina</td>
			<td style='font-size:$letra px !important'> Fecha de Pago : $FechaPago</td>
		</tr>
		<tr>
			<td style='font-size:$letra px !important'> Fecha Inicial Pago : $FechaInicialPago</td>
			<td style='font-size:$letra px !important'> Fecha Final Pago : $FechaFinalPago
			</td>
		</tr>
		<tr>
			<td style='font-size:$letra px !important'> Dias Pagados : $NumDiasPagados</td>
			<td style='font-size:$letra px !important'> Total Percepciones : $TotalPercepciones
			</td>
		</tr>
		<tr>
			<td style='font-size:$letra px !important'> Total Deducciones : $TotalDeducciones</td>
			<td style='font-size:$letra px !important'> Total Otros Pagos : $TotalOtrosPagos
			</td>
		</tr>
	</table>
</div>    
        ";
	
	// EMISOR
	$NominaEmisor='';
	foreach ($xml->xpath('//nomina12:Emisor') as $Emisor)
    {
		$Curp = autoformato_impresion($Emisor['Curp']);
		$RegistroPatronal= autoformato_impresion($Emisor['RegistroPatronal']);
		$RfcPatronOrigen= autoformato_impresion($Emisor['RfcPatronOrigen']);
		$OrigenRecurso = '';
		$MontoRecurso = '';
		
		$entidad = $xml->xpath('//nomina12:EntidadSNCF');
		if(!empty($entidad))
		{
			$entidad = $entidad[0];
			$OrigenRecurso = autoformato_impresion($entidad['OrigenRecurso']);
			$OrigenRecurso = $catOrigenRegusro[$OrigenRecurso];
			$MontoRecurso = autoformato_impresion($entidad['MontoRecursoPropio']);
		}
		
		$NominaEmisor.="<tr>
	<td style='font-size:$letra px !important'>$Curp</td>
	<td style='font-size:$letra px !important'>$RegistroPatronal</td>
	<td style='font-size:$letra px !important'>$RfcPatronOrigen</td>
	<td style='font-size:$letra px !important'>$OrigenRecurso</td>
	<td style='font-size:$letra px !important'>$MontoRecurso</td>
</tr>";
       
    }
    
    if(strlen($NominaEmisor)>10)
    {
        $NominaEmisor="
        <b>EMISOR:</b>
<table width='100%'>
	<tr class='factura_detalles_cabecera'  >
		<td style='font-size:$letra px !important'>CURP</td>
		<td style='font-size:$letra px !important'>REGISTRO PATRONAL</td>
		<td style='font-size:$letra px !important'>RFC PATRON ORIGEN</td>
		<td style='font-size:$letra px !important'>ORIGEN RECURSO</td>
		<td style='font-size:$letra px !important'>MONTO</td>
	</tr>
            $NominaEmisor
</table>
        ";
    }
    
    // RECEPTOR
	$NominaReceptor='';
	foreach ($xml->xpath('//nomina12:Receptor') as $Receptor)
    {
		$Curp = autoformato_impresion($Receptor['Curp']);
		//$NumeroSeguridadSocial= autoformato_impresion($Receptor['NumeroSeguridadSocial']);
        $NumeroSeguridadSocial= $Receptor['NumSeguridadSocial'];
        $FechaInicioRelLaboral= autoformato_impresion($Receptor['FechaInicioRelLaboral']);
		$Antiguedad= autoformato_impresion($Receptor[utf8_encode('Antigüedad')]);
		$TipoContrato= autoformato_impresion($Receptor['TipoContrato']);
		$TipoContrato = $catTipoContrato[$TipoContrato];
		$Sindicalizado= autoformato_impresion($Receptor['Sindicalizado']);
		$TipoJornada= autoformato_impresion($Receptor['TipoJornada']);
		$TipoJornada = $catTipoJornada[$TipoJornada];
		$TipoRegimen= autoformato_impresion($Receptor['TipoRegimen']);
		$TipoRegimen = $catTipoRegimen[$TipoRegimen];
		$NumEmpleado= autoformato_impresion($Receptor['NumEmpleado']);
		$Departamento= autoformato_impresion($Receptor['Departamento']);
		$Puesto= autoformato_impresion($Receptor['Puesto']);
		$RiesgoPuesto= autoformato_impresion($Receptor['RiesgoPuesto']);
		$RiesgoPuesto = $catRiesgoPuesto[$RiesgoPuesto];
		$PeriodicidadPago= autoformato_impresion($Receptor['PeriodicidadPago']);
		$PeriodicidadPago = $catPeriodicidadPago[$PeriodicidadPago];
		$Banco= autoformato_impresion($Receptor['Banco']);
		$Banco = $catBanco[$Banco];
		$CuentaBancaria= autoformato_impresion($Receptor['CuentaBancaria']);
		$SalarioBaseCotApor= autoformato_impresion($Receptor['SalarioBaseCotApor']);
		$SalarioDiarioIntegrado= autoformato_impresion($Receptor['SalarioDiarioIntegrado']);
		$ClaveEntFed= autoformato_impresion($Receptor['ClaveEntFed']);
		$ClaveEntFed = $catEntidad[$ClaveEntFed];
		
		$NominaReceptor="<table width='100%'>
  <tr>
    <th colspan='4' style='background-color: black; color:white;'>RECEPTOR</th>
  </tr>
  <tr>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>CURP</td>
    <td style='font-size:$letra px !important'>$Curp</td>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>NUM SEGURO SOCIAL</td>
    <td style='font-size:$letra px !important'>$NumeroSeguridadSocial</td>
  </tr>
  <tr>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>FECHA INICIO REL LABORAL</td>
    <td style='font-size:$letra px !important'>$FechaInicioRelLaboral</td>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>ANTIGUEDAD</td>
    <td style='font-size:$letra px !important'>$Antiguedad</td>
  </tr>
  <tr>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>TIPO CONTRATO</td>
    <td style='font-size:$letra px !important'>$TipoContrato</td>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>SINDICALIZADO</td>
    <td style='font-size:$letra px !important'>$Sindicalizado</td>
  </tr>
  <tr>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>TIPO JORNADA</td>
    <td style='font-size:$letra px !important'>$TipoJornada</td>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>TIPO REGIMEN</td>
    <td style='font-size:$letra px !important'>$TipoRegimen</td>
  </tr>
  <tr>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>NUM EMPLEADO</td>
    <td style='font-size:$letra px !important'>$NumEmpleado</td>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>DEPARTAMENTO</td>
    <td style='font-size:$letra px !important'>$Departamento</td>
  </tr>
  <tr>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>PUESTO</td>
    <td style='font-size:$letra px !important'>$Puesto</td>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>RIESGO PUESTO</td>
    <td style='font-size:$letra px !important'>$RiesgoPuesto</td>
  </tr>
  <tr>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>PERIODICIDAD PAGO</td>
    <td style='font-size:$letra px !important'>$PeriodicidadPago</td>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>BANCO</td>
    <td style='font-size:$letra px !important'>$Banco</td>
  </tr>
  <tr>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>CUENTA BANCARIA</td>
    <td style='font-size:$letra px !important'>$CuentaBancaria</td>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>SALARIO BASE COT APOR</td>
    <td style='font-size:$letra px !important'>$SalarioBaseCotApor</td>
  </tr>
  <tr>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>SALARIO DIARIO INTEGRADO</td>
    <td style='font-size:$letra px !important'>$SalarioDiarioIntegrado</td>
    <td class='factura_detalles_cabecera' style='font-size:$letra px !important'>ENTIDAD FEDERATIVA</td>
    <td style='font-size:$letra px !important'>$ClaveEntFed</td>
  </tr>
</table>";
		
		/*$NominaReceptor.="<tr>
	<td style='font-size:$letra px !important'>$Curp</td>
	<td style='font-size:$letra px !important'>$NumeroSeguridadSocial</td>
	<td style='font-size:$letra px !important'>$FechaInicioRelLaboral</td>
	<td style='font-size:$letra px !important'>$Antiguedad</td>
	<td style='font-size:$letra px !important'>$TipoContrato</td>
	<td style='font-size:$letra px !important'>$Sindicalizado</td>
	<td style='font-size:$letra px !important'>$TipoJornada</td>
	<td style='font-size:$letra px !important'>$TipoRegimen</td>
	<td style='font-size:$letra px !important'>$NumEmpleado</td>
	<td style='font-size:$letra px !important'>$Departamento</td>
	<td style='font-size:$letra px !important'>$Puesto</td>
	<td style='font-size:$letra px !important'>$RiesgoPuesto</td>
	<td style='font-size:$letra px !important'>$PeriodicidadPago</td>
	<td style='font-size:$letra px !important'>$Banco</td>
	<td style='font-size:$letra px !important'>$CuentaBancaria</td>
	<td style='font-size:$letra px !important'>$SalarioBaseCotApor</td>
	<td style='font-size:$letra px !important'>$SalarioDiarioIntegrado</td>
	<td style='font-size:$letra px !important'>$ClaveEntFed</td>
</tr>";*/
       
    }
    
    if(strlen($NominaReceptor)>10)
    {
        /*$NominaReceptor="
        <b>RECEPTOR:</b>
<table width='100%'>
	<tr class='factura_detalles_cabecera'  >
		<td style='font-size:$letra px !important'>CURP</td>
		<td style='font-size:$letra px !important'>NUM SEG SOCIAL</td>
		<td style='font-size:$letra px !important'>FECHA INICIO REL LABORAL</td>
		<td style='font-size:$letra px !important'>ANTIGUEDAD</td>
		<td style='font-size:$letra px !important'>TIPO CONTRATO</td>
		<td style='font-size:$letra px !important'>SINDICALIZADO</td>
		<td style='font-size:$letra px !important'>TIPO JORNADA</td>
		<td style='font-size:$letra px !important'>TIPO REGIMEN</td>
		<td style='font-size:$letra px !important'>NUM EMPLEADO</td>
		<td style='font-size:$letra px !important'>DEPARTAMENTO</td>
		<td style='font-size:$letra px !important'>PUESTO</td>
		<td style='font-size:$letra px !important'>RIESGO PUESTO</td>
		<td style='font-size:$letra px !important'>PERIODICIDAD PAGO</td>
		<td style='font-size:$letra px !important'>BANCO</td>
		<td style='font-size:$letra px !important'>CUENTA BANCARIA</td>
		<td style='font-size:$letra px !important'>SALARIO BASE COT APOR</td>
		<td style='font-size:$letra px !important'>SALARIO DIARIO INTEGRADO</td>
		<td style='font-size:$letra px !important'>ENTIDAD FED</td>
	</tr>
            $NominaReceptor
</table>
        ";*/
    }
    
    //NOMINAS SUBCONTRATACION
    $NominaSubcontratacion='';
    foreach ($xml->xpath('//nomina12:SubContratacion') as $Subcontratacion)
    {
        $RfcLabora= autoformato_impresion($Subcontratacion['RfcLabora']);
        $PorcentajeTiempo= autoformato_impresion($Subcontratacion['PorcentajeTiempo']);
        
        
        $NominaSubcontratacion.="<tr>
	<td style='font-size:$letra px !important'>$RfcLabora</td>
	<td style='font-size:$letra px !important'>$PorcentajeTiempo</td>
</tr>";
        

    }
    if(strlen($NominaSubcontratacion)>10)
    {
        $NominaSubcontratacion="
        <b>SUBCONTRATACION:</b>
<table width='100%'>
	<tr class='factura_detalles_cabecera'  >
		<td style='font-size:$letra px !important'>RFC LABORA</td>
		<td style='font-size:$letra px !important'>PORCENTAJE TIEMPO</td>
	</tr>
            $NominaSubcontratacion
</table>
        ";
    }
    
    //NOMINAS PERCEPCIONES
    $NominaPercepciones='';
    foreach ($xml->xpath('//nomina12:Percepcion') as $Percepciones)
    {
        $TipoPercepcion= autoformato_impresion($Percepciones['TipoPercepcion']);
        $Clave= autoformato_impresion($Percepciones['Clave']);
        $Concepto= autoformato_impresion($Percepciones['Concepto']);
        $ImporteGravado= autoformato_impresion($Percepciones['ImporteGravado']);
        $ImporteExento= autoformato_impresion($Percepciones['ImporteExento']);
        
        
        $NominaPercepciones.="<tr>
	<td style='font-size:$letra px !important'>$TipoPercepcion</td>
	<td style='font-size:$letra px !important'>$Clave</td>
	<td style='font-size:$letra px !important'>$Concepto</td>
	<td style='font-size:$letra px !important'>$ImporteGravado</td>
	<td style='font-size:$letra px !important'>$ImporteExento</td>
</tr>";
        

    }
    if(strlen($NominaPercepciones)>10)
    {
        $NominaPercepciones="
        <b>PERCEPCIONES:</b>
<table width='100%'>
	<tr class='factura_detalles_cabecera'  >
		<td style='font-size:$letra px !important'>TP</td>
		<td style='font-size:$letra px !important'>CLAVE</td>
		<td style='font-size:$letra px !important' >CONCEPTO</td>
		<td style='font-size:$letra px !important'>IMP GRAVADO</td>
		<td style='font-size:$letra px !important'>IMP EXCENTO</td>
	</tr>
            $NominaPercepciones
</table>
        ";
    }


//NOMINAS DEDUCCIONES
    $NominaDeducciones='';
    foreach ($xml->xpath('//nomina12:Deduccion') AS $Deducciones)
    {
        $TipoDeduccion= autoformato_impresion($Deducciones['TipoDeduccion']);
        $Clave= autoformato_impresion($Deducciones['Clave']);
        $Concepto= autoformato_impresion($Deducciones['Concepto']);
        $Importe= autoformato_impresion($Deducciones['Importe']);
        
        $NominaDeducciones.="<tr>
	<td style='font-size:$letra px !important'>$TipoDeduccion</td>
	<td style='font-size:$letra px !important'>$Clave</td>
	<td style='font-size:$letra px !important'>$Concepto</td>
	<td style='font-size:$letra px !important'>$Importe</td>
</tr>";
        

    }
    if(strlen($NominaDeducciones)>10)
    {
        $NominaDeducciones="
        <b>DEDUCCIONES:</b>
<table width='100%'>
	<tr class='factura_detalles_cabecera'  >
		<td style='font-size:$letra px !important'>TP</td>
		<td style='font-size:$letra px !important'>CLAVE</td>
		<td style='font-size:$letra px !important'>CONCEPTO</td>
		<td style='font-size:$letra px !important'>IMPORTE</td>
	</tr>
            $NominaDeducciones
</table>
        ";
    }



//NOMINAS HORAS EXTRA
    $NominaHorasExtras='';
    foreach ($xml->xpath('//nomina12:HorasExtra') AS $HoraExtra)
    {
        
        $Dias= autoformato_impresion($HoraExtra['Dias']);
        $TipoHoras= autoformato_impresion($HoraExtra['TipoHoras']);
        $HorasExtra= autoformato_impresion($HoraExtra['HorasExtra']);
        $TipoHoras = $catTipoHoras[$TipoHoras];
        
        $NominaHorasExtras.="<tr>
	<td style='font-size:$letra px !important'>$Dias</td>
	<td style='font-size:$letra px !important'>$TipoHoras</td>
	<td style='font-size:$letra px !important'>$HorasExtra</td>
</tr>";

        

    }
    if(strlen($NominaHorasExtras)>10)
    {
        $NominaHorasExtras="
        <b>HORAS EXTRA:</b>
            <table width='100%'>
       <tr class='factura_detalles_cabecera'  >
        <td style='font-size:$letra px !important'>DIAS</td>
        <td style='font-size:$letra px !important'>TIPO</td>
        <td style='font-size:$letra px !important'>HORAS EXTRA</td>
       </tr>

            $NominaHorasExtras
            </table>
        ";
    }



//NOMINAS INCAPACIDADES
    $NominaIncapacidades='';
    foreach ($xml->xpath('//nomina12:Incapacidad') AS $Incapacidad)
    {
        $DiasIncapacidad= autoformato_impresion($Incapacidad['DiasIncapacidad']);
        $TipoIncapacidad= autoformato_impresion($Incapacidad['TipoIncapacidad']);
        $TipoIncapacidad = $catTipoIncapacidad[$TipoIncapacidad];
        $Descuento= autoformato_impresion($Incapacidad['ImporteMonetario']);        
        
        $NominaIncapacidades.="<tr>
	<td style='font-size:$letra px !important'>$DiasIncapacidad</td>
	<td style='font-size:$letra px !important'>$TipoIncapacidad</td>
	<td style='font-size:$letra px !important'>$Descuento</td>
</tr>";

        

    }
    if(strlen($NominaIncapacidades)>10)
    {
        $NominaIncapacidades="
        <b>INCAPACIDADES:</b>
            <table width='100%'>
       <tr class='factura_detalles_cabecera'  >
        <td style='font-size:$letra px !important'>DIAS</td>
        <td style='font-size:$letra px !important'>TIPO</td>
        <td style='font-size:$letra px !important'>IMPORTE</td>
       </tr>

            $NominaIncapacidades
            </table>
        ";
    }
    
    //NOMINAS OTROS PAGOS
    $NominaOtrosPagos='';
    foreach ($xml->xpath('//nomina12:OtroPago') AS $OtroPago)
    {
		$TipoOtroPago= autoformato_impresion($OtroPago['TipoOtroPago']);
		$TipoOtroPago = $catTipoOtroPago[$TipoOtroPago];
        $Clave= autoformato_impresion($OtroPago['Clave']);
        $Concepto= autoformato_impresion($OtroPago['Concepto']);
        $Importe= autoformato_impresion($OtroPago['Importe']);
        
        $NominaOtrosPagos.="<tr>
	<td style='font-size:$letra px !important'>$Clave</td>
	<td style='font-size:$letra px !important'>$TipoOtroPago</td>
	<td style='font-size:$letra px !important'>$Concepto</td>
	<td style='font-size:$letra px !important'>$Importe</td>
</tr>";
    }
    if(strlen($NominaOtrosPagos)>10)
    {
        $NominaOtrosPagos="
        <b>OTROS PAGOS:</b>
            <table width='100%'>
       <tr class='factura_detalles_cabecera'  >
        <td style='font-size:$letra px !important'>CLAVE</td>
        <td style='font-size:$letra px !important'>TIPO OTRO PAGO</td>
        <td style='font-size:$letra px !important'>CONCEPTO</td>
        <td style='font-size:$letra px !important'>IMPORTE</td>
       </tr>

            $NominaOtrosPagos
            </table>
        ";
    }
    //NOMINAS JUBILACION
    $NominaJubilacion='';
    foreach ($xml->xpath('//nomina12:JubilacionPensionRetiro') as $Jubilacion)
    {
        $TotalUnaExhibicion= autoformato_impresion($Jubilacion['TotalUnaExhibicion']);
        $TotalParcialidad= autoformato_impresion($Jubilacion['TotalParcialidad']);
        $MontoDiario= autoformato_impresion($Jubilacion['MontoDiario']);
        $IngresoAcumulable= autoformato_impresion($Jubilacion['IngresoAcumulable']);
        $IngresoNoAcumulable= autoformato_impresion($Jubilacion['IngresoNoAcumulable']);
        
        
        $NominaJubilacion.="<tr>
	<td style='font-size:$letra px !important'>$TotalUnaExhibicion</td>
	<td style='font-size:$letra px !important'>$TotalParcialidad</td>
	<td style='font-size:$letra px !important'>$MontoDiario</td>
	<td style='font-size:$letra px !important'>$IngresoAcumulable</td>
	<td style='font-size:$letra px !important'>$IngresoNoAcumulable</td>
</tr>";
        

    }
    if(strlen($NominaJubilacion)>10)
    {
        $NominaJubilacion="
        <b>JUBILACION/PENCION/RETIRO:</b>
<table width='100%'>
	<tr class='factura_detalles_cabecera'  >
		<td style='font-size:$letra px !important'>TOTAL UNA EXHIBICION</td>
		<td style='font-size:$letra px !important'>TOTAL PARCIALIDAD</td>
		<td style='font-size:$letra px !important' >MONTO DIARIO</td>
		<td style='font-size:$letra px !important'>INGRESO ACUMULABLE</td>
		<td style='font-size:$letra px !important'>INGRESO NO ACUMULABLE</td>
	</tr>
            $NominaJubilacion
</table>
        ";
    }
    
    //NOMINAS SEPARACION INDEMNIZACION
    $NominaSeparacion='';
    foreach ($xml->xpath('//nomina12:SeparacionIndemnizacion') as $Separacion)
    {
        $TotalPagado= autoformato_impresion($Separacion['TotalPagado']);
        $NumAñosServicio= autoformato_impresion($Separacion['NumAñosServicio']);
        $UltimoSueldoMensOrd= autoformato_impresion($Separacion['UltimoSueldoMensOrd']);
        $IngresoAcumulable= autoformato_impresion($Separacion['IngresoAcumulable']);
        $IngresoNoAcumulable= autoformato_impresion($Separacion['IngresoNoAcumulable']);
        
        
        $NominaSeparacion.="<tr>
	<td style='font-size:$letra px !important'>$TotalPagado</td>
	<td style='font-size:$letra px !important'>$NumAñosServicio</td>
	<td style='font-size:$letra px !important'>$UltimoSueldoMensOrd</td>
	<td style='font-size:$letra px !important'>$IngresoAcumulable</td>
	<td style='font-size:$letra px !important'>$IngresoNoAcumulable</td>
</tr>";
        

    }
    if(strlen($NominaSeparacion)>10)
    {
        $NominaSeparacion="
        <b>SEPARACION/INDEMNIZACION:</b>
<table width='100%'>
	<tr class='factura_detalles_cabecera'  >
		<td style='font-size:$letra px !important'>TOTAL PAGADO</td>
		<td style='font-size:$letra px !important'>AÑOS SERVICIO</td>
		<td style='font-size:$letra px !important' >ULTIMO SUELDO MENS ORD</td>
		<td style='font-size:$letra px !important'>INGRESO ACUMULABLE</td>
		<td style='font-size:$letra px !important'>INGRESO NO ACUMULABLE</td>
	</tr>
            $NominaSeparacion
</table>
        ";
    }
    
    // NOMINA ACCIONES O TITULOS
    $NominaAcciones='';
    foreach ($xml->xpath('//nomina12:AccionesOTitulos') as $Accion)
    {
        $ValorMercado= autoformato_impresion($Accion['ValorMercado']);
        $PrecioAlOtorgarse= autoformato_impresion($Accion['PrecioAlOtorgarse']);
        
        
        $NominaAcciones.="<tr>
	<td style='font-size:$letra px !important'>$ValorMercado</td>
	<td style='font-size:$letra px !important'>$PrecioAlOtorgarse</td>
</tr>";
        

    }
    if(strlen($NominaAcciones)>10)
    {
        $NominaAcciones="
        <b>ACCIONES O TITULOS:</b>
<table width='100%'>
	<tr class='factura_detalles_cabecera'  >
		<td style='font-size:$letra px !important'>VALOR DE MERCADO</td>
		<td style='font-size:$letra px !important'>PRECIO AL OTORGARSE</td>
	</tr>
            $NominaAcciones
</table>
        ";
    }
    
    // NOMINA SUBSIDIO AL EMPLEO
    $NominaSubsidio='';
    foreach ($xml->xpath('//nomina12:SubsidioAlEmpleo') as $Subsidio)
    {
        $SubsidioCausado= autoformato_impresion($Subsidio['SubsidioCausado']);        
        
        $NominaSubsidio.="<tr>
	<td style='font-size:$letra px !important'>$SubsidioCausado</td>
</tr>";
        

    }
    if(strlen($NominaSubsidio)>10)
    {
        $NominaSubsidio="
        <b>SUBSIDIO:</b>
<table width='100%'>
	<tr class='factura_detalles_cabecera'  >
		<td style='font-size:$letra px !important'>SUBSIDIO CAUSADO</td>
	</tr>
            $NominaSubsidio
</table>
        ";
    }
    
    // NOMINA COMPENSACION
    $NominaCompensacion='';
    foreach ($xml->xpath('//nomina12:CompensacionSaldosAFavor') as $Compensacion)
    {
        $SaldoAFavor= autoformato_impresion($Compensacion['SaldoAFavor']);
        $Ano= autoformato_impresion($Compensacion[utf8_encode('Año')]);
        $RemanenteSalFav= autoformato_impresion($Compensacion['RemanenteSalFav']);
        
        $NominaCompensacion.="<tr>
	<td style='font-size:$letra px !important'>$SaldoAFavor</td>
	<td style='font-size:$letra px !important'>$Ano</td>
	<td style='font-size:$letra px !important'>$RemanenteSalFav</td>
</tr>";
        

    }
    if(strlen($NominaCompensacion)>10)
    {
        $NominaCompensacion="
        <b>COMPENSACION:</b>
<table width='100%'>
	<tr class='factura_detalles_cabecera'  >
		<td style='font-size:$letra px !important'>SALDO A FAVOR</td>
		<td style='font-size:$letra px !important'>AÑO</td>
		<td style='font-size:$letra px !important'>REMANENTE</td>
	</tr>
            $NominaCompensacion
</table>
        ";
    }
}


$nominas_txt="
<table>
<tr valign='top'>
    <td width='50%' valign='top'>
		$NominaEmisor
        $NominaPercepciones
        $NominaHorasExtras
        $NominaOtrosPagos
        $NominaAcciones
        $NominaSubsidio
    </td>
    <td width='50%'>
		$NominaJubilacion
        $NominaDeducciones
        $NominaIncapacidades
        $NominaSeparacion
        $NominaSubcontratacion
        $NominaCompensacion
    </td>

</tr>
</table>
$NominaReceptor
";
$emisor_municipio2=trim(strtolower($emisor_municipio2));
$emisor_municipio2=str_replace(' ','',$emisor_municipio2);
$emisor_municipio2=str_replace('.','',$emisor_municipio2);
$emisor_municipio2=str_replace(',','',$emisor_municipio2);

$emisor_localidad2=trim(strtolower($emisor_localidad2));
$emisor_localidad2=str_replace(' ','',$emisor_localidad2);
$emisor_localidad2=str_replace('.','',$emisor_localidad2);
$emisor_localidad2=str_replace(',','',$emisor_localidad2);


if($emisor_municipio2==$emisor_localidad2)
{
    $emisor_localidad='';
}

//////////  DISEÑO ////////////

if($version=='3.2')
{
    $Emisor="
    <div class='factura_emisor factura_cuadro'>
        <div class='factura_titulo_ch'>EMISOR:</div>
        <div class='factura_titulo_empresa'>$emisor_nombre </div>
        <div> RFC: <b>$emisor_rfc</b></div>
        
        $emisor_calle $emisor_noExterior $emisor_noInterior, $emisor_colonia CP:$emisor_CP
        <br/>
        $emisor_municipio $emisor_localidad,
        $emisor_estado,
        $emisor_pais
        
    </div>
    ";
}
if($version=='3.3')
{
    $Emisor="
    <div class='factura_emisor factura_cuadro'>
        <div class='factura_titulo_ch'>EMISOR:</div>
        <div class='factura_titulo_empresa'>$emisor_nombre </div>
        <div> RFC: <b>$emisor_rfc</b></div>
        
        <br/>
     </div>
    ";
}
    
$ciudad_estado="$emisor_municipio $emisor_localidad $emisor_estado,";
if($expedido_municipio==$expedido_localidad)
{
    $expedido_localidad='';
}

$ExpedidoEn='';

$ExpedidoEn="
<div class='factura_expedidoen factura_cuadro_linea'>
    <span class='factura_titulo_ch'>EXPEDIDO EN:</span>
    $expedido_calle $expedido_noExterior $expedido_noInterior, $expedido_colonia 
    $expedido_municipio $expedido_localidad, $expedido_estado, $expedido_pais CP:$expedido_CP
</div>

";

if($version=='3.3')
{
    $ExpedidoEn='<hr/>';
}

$idreceptor=$datosfacturas['idreceptor'];
$Fiscal_Orientacion=$datosreceptor['Fiscal_Orientacion'];

if($receptor_municipio==$receptor_localidad)
{
    $receptor_localidad='';
}

if($version=='3.2')
{
    $Receptor="
    <div class='factura_receptor factura_cuadro '>
        <div class='factura_titulo_ch'>RECEPTOR:</div>
        <div class='factura_titulo_empresa'>$receptor_nombre  </div>
        RFC: <b> $receptor_rfc </b><br/>
        
        $receptor_calle $receptor_noExterior $receptor_noInterior $Fiscal_Orientacion $receptor_colonia CP:$receptor_CP
        <br/>
        $receptor_municipio  $receptor_localidad,
         $receptor_estado,
         $receptor_pais
    
    </div>
    ";
}
if($version=='3.3')
{
    $Receptor="
    <div class='factura_receptor factura_cuadro '>
        <div class='factura_titulo_ch'>RECEPTOR:</div>
        <div class='factura_titulo_empresa'>$receptor_nombre  </div>
        RFC: <b> $receptor_rfc </b><br/>
        Uso CFDI: $uso_CFDi
        
    </div>
    ";
}
$DatosGenerales="
<div class='factura_titulo_serie_folio'>$titulo</div>
<div class='factura_datosgenerales'>

<div class='factura_titulo_serie_folio'>$tipo_factura : $serie$folio</div>

<div> Folio Fiscal : $timbre_uuid  </div>
<div> Numero Certificado CSD : $certificado_no  </div>
<div> Lugar y Fecha: $LugarExpedicion $fecha_expedicion  </div>

</div>
";
//$logo="<div class='logo'><img src='{URL}/$logo'></div>";
if(!file_exists($logo))
{
    $logo="c:/cfdipdf/transparente.gif";
}
else
{
    $conflogo['max']=220;
    if(function_exists('ver_imagen_mash'))
    {
        $logo=ver_imagen_mash($logo,250,0,$conflogo);
    }
    else
    {
        $logo=$logo;
    }    
}


$cabecera="
    <table width='100%'>
        <tr valign='top'>
            <td width='260'><img src='{URL}/$logo'></td>

            <td >$DatosGenerales</td>
        </tr>
    </table>
    $ExpedidoEn
    <table width='100%'>
        <tr valign='top'>
            <td width='50%'>$Emisor</td>
            <td width='50%'>$Receptor</td>
        </tr>
    </table>

";
//$nomina_general
$certificado_key= $datosjson['datoscertificado']['SAT_Llave_PEM'];

//cfd_lee_cadena($sello,$certificado_key,$timbre_noCertificadoSAT);
//echo base64_decode($timbre_selloSAT);

    $cadena_sat="||$timbre_version|$timbre_uuid|$timbre_fecha|$timbre_selloCFD|$timbre_noCertificadoSAT||";

$longitud=95;
$sello = wordwrap($sello,$longitud,'<br>',true);
$timbre_selloSAT = wordwrap($timbre_selloSAT,$longitud,'<br>',true);
$cadena_sat = wordwrap($cadena_sat,$longitud,'<br>',true);
//$sello = wordwrap($sello,$longitud,'<br>',true);


$idsession=$datosfacturas['idtpv_session'];
$idempresa_atendio=$datosfacturas['idempresa_atendio'];
$idcliente=$datosfacturas['idcliente'];
$idempresa=$datosfacturas['idempresa'];

//        $timbre_selloSAT = $sellosat=$tfd['selloSAT'];

global $masheditor;
//print_r($masheditor);
list($ano,$mes,$dia)=explode("-",date('Y-m-d'));
$ruta_instalacion=$masheditor['carpeta_instalacion']."/$EMISOR_NOMBRE/";

$archivo_png=str_replace('.xml','.png',$xml_archivo);
$archivo_png=str_replace('.XML','.PNG',$archivo_png);


if(function_exists('libreria_mash'))
{
    libreria_mash('num2letras');
    
}
else
{

    include_once 'num2letras.php';
}

    switch($Moneda)
    {
        case 'MXN' :  $moneda_txt='PESOS'; break;
        case 'USD' :  $moneda_txt='DOLAR'; break;
        case 'EUR' :  $moneda_txt='EUROS'; break;
        default :  $moneda_txt='PESOS'; break;
    }
    
    $numeroletras=num2letras($total,'  '); 
    
    //ES PAGO
    if($TipoDeComprobante =='P'){
        $numeroletras=num2letras($Monto_Pago,'  ');
    }   


if(intval($NumCtaPago)>0)
    $NumCtaPago_txt="CUENTA : $NumCtaPago";

//echo    "$ruta_instalacion/$archivo_png";
$ruta_instalacion=str_replace('//','/',$ruta_instalacion);
$ruta_instalacion=str_replace('//','/',$ruta_instalacion);
$ruta_instalacion=str_replace('//','/',$ruta_instalacion);

//echo    "$ruta_instalacion/$archivo_png";
$ruta_instalacion='{URL}';

$sellos_pie="

<div class='factura_sellos factura_cuadro '>
<table width='100%' border=0>
    <tr valign='top'>
        <td width=200px;>
            <img src='$ruta_instalacion/$archivo_png'><br/>
            
        </td>
        <td>
            <div class='factura_sellos_txt'>
            CANTIDAD CON LETRA: $numeroletras $moneda_txt ($Moneda)<br>
            "; 
            
            if($TipoDeComprobante !='P')
                  $sellos_pie.="<b>METODO PAGO: $metodo_pago | FORMA PAGO: $forma_pago | $NumCtaPago_txt  </b><br/>";
                    
            $sellos_pie.="<b>$html_parcialidades</b>
                <b>REGIMEN FISCAL : </b>$regimen_fiscal  <b>Fecha Timbrado : </b>$timbre_fecha <br/> 
                <b>SELLO : </b><br/>$sello <br/>
                <b>SELLO SAT : </b><br/>$timbre_selloSAT <br/>

                <b>Numero Certificado SAT : </b> $timbre_noCertificadoSAT <br/>
                <b>Cadena Original</b><br/><br/>
                $cadena_sat 
                <br/>

<b>Este documento es una representación impresa de un CFDI</b> EFECTOS FISCALES AL PAGO 
            </div>
            <br/>
$referencia $barcode_factura 
        </td>
    
    </tr>
</table>

</div>
";
 
$importeneto=(float)$subtotal;
//echo "$importeneto=$subtotal-$descuento";
//mash $importeneto=sprintf('%1.2f',$importeneto);


if(strlen($nota_impresa)>5)
{
    $notas_impresas="
        <div class='factura_sellos factura_cuadro nota'>
        NOTA: <br/>
        $nota_impresa
        </div>
    ";    
}


$desc1= $datosfacturas['descuento_adicional_porcentaje'];
$desc2= $datosfacturas['descuento_formapago_porcentaje'];


$importeneto_=number_format((string)$importeneto,2);

if($descuento>0)
{
        $descuento_txt_="
                        <tr>
        
                            <td class='factura_totales'>
                            DESCUENTO $<br>
                            
                            </td>
                            <td class='factura_totales'>
                             $descuento_
                            </td>
                        </tr>
        ";
}

/*


                        <tr>
        
                            <td class='factura_totales'>
                            IMPORTE NETO $
                            </td>
                            <td class='factura_totales'>
                             $importeneto_
                            </td>
                        </tr>

*/




if($CURP!='')
{
//    $retenciones_txt='';
}



$pie="
<table width='100%'>
    <tr>
        <td valign='top' >
            $notas_impresas
        </td>
        <td width='300px' >
            
                    <table width='300px' >
                        <tr >
                            <td class='factura_totales'>
                            IMPORTE $
                            </td>
                            <td class='factura_totales'>
                             $subtotal_
                            </td>
                        </tr>
$descuento_txt_
$iva_txt
                        $retenciones_txt
                        <tr>
        
                            <td class='factura_totales'>
                            TOTAL $
                            </td>
                            <td class='factura_totales'>
                             $total_<br/>$Moneda
                             
                            </td>
                            
                        </tr>
                        
                    </table> 
          
        </td>
    </tr>
</table>

";

//ES UN PAGO
if($TipoDeComprobante =='P')
{
    $pie="
    <table width='100%'>
        <tr>
            <td valign='top' >
                $notas_impresas
            </td>
            <td width='300px' >
                
                        <table width='300px' >
                            <tr >
                                <td class='factura_totales'>

                                </td>
                                <td class='factura_totales'>
    
                                </td>
                            </tr>
                            <tr>
            
                                <td class='factura_totales'>
    
                                </td>
                                <td class='factura_totales'>
    
                                 
                                </td>
                                
                            </tr>
                            
                        </table> 
              
            </td>
        </tr>
    </table>
    
    ";

}

$idfactura2=sprintf('%06d',$idfactura);
$cancelado='';


/*
    $sql="
    SELECT *
    FROM
      `multi_facturas`
    WHERE
      `multi_facturas`.`SERIE` = $serie AND 
      `multi_facturas`.`FOLIO` = $folio    
    ";
*/
          
          
          
if($datosfacturas['factura_cancelada']==1)
{
    $cancelado_fecha=$datosfacturas['factura_cancelada_fecha'];
    $cancelado_motivo=$datosfacturas['factura_cancelada_motivo'];
    $cancelado_msg_pac=$datosfacturas['factura_cancelada_pac_msg'];

    $cancelado="
    <div class='factura_cancelada'>
    <h4>FACTURA CANCELADA</h4> <br/>
    FECHA CANCELACION : $cancelado_fecha  <br/>
    MOTIVO : $cancelado_motivo  <br/>
    MENSAJE DEL TIMBRADO : $cancelado_msg_pac  <br/>
    
    </div>
    ";
}

if($datosfacturas['factura_cancelada']==2)
{
    $cancelado_fecha=$datosfacturas['factura_cancelada_fecha'];
    $cancelado_motivo=$datosfacturas['factura_cancelada_motivo'];
    $cancelado_msg_pac=$datosfacturas['factura_cancelada_pac_msg'];

    $cancelado="
    <div class='factura_cancelada'>
    <h4>FACTURA PENDIENTE DE CANCELAR</h4> <br/>
    <h4>NO ESTA INCLUIDO EN EL REPORTE DE VENTAS</h4> <br/>
    FECHA CANCELACION : $cancelado_fecha  <br/>
    MOTIVO : $cancelado_motivo  <br/>
    MENSAJE DEL TIMBRADO : $cancelado_msg_pac  <br/>
    
    </div>
    ";
}


//if($certificado_no==20001000000100005867)
if($certificado_no==20001000000300022815)
{
    $cancelado="
    <div class='factura_cancelada'>
<br/><br/>    
PRUEBA DEL SISTEMA
<br/>
FACTURA NO VALIDA ANTE EL SAT
<br/><br/><br/>    
    </div>
    ";
}
/*
$leyenda="
<div class='factura_leyenda' style='font-size:10px !important;'>
Por este pagare me(nos) obligo(amos) a pagar incondicionalmente el dia ___________________ en esta ciudad de $ciudad_estado.
o en cualquier otra plaza que se me(nos) requiera a la orden de $emisor_nombre
la cantidad de $________________ valor recibido a mi(nuestra) entrega satisfaccion, queda convenida que en caso de mora
el presente pagare causara un interes ____% mensual hasta la liquidacion.
<br><br>
FIRMA CLIENTE : _____________________________ <br/>

</div>
";
*/
if($CURP!='')
{
    $leyenda='';
}


$valor.="
$cabecera
<div class=factura_detalles>
$desgloce
$nominas_txt
$INE_general
$HTML_PAGOS
$nomina_general
$cancelado
</div>
$pie
$sellos_pie
$barcode_factura
$leyenda 
";

global $masheditor;
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' OR count($masheditor)==0) 
    {
        $valor=str_replace('{URL}/','',$valor);
    }

    return $valor;
}
///////////////////////////////////////////////////////////////////////////////
function autoformato_impresion($txt)
{
    //$txt=utf8_decode(utf8_decode($txt));
    $txt=utf8_decode($txt);
    return $txt;
}
///////////////////////////////////////////////////////////////////////////////

function object2array($object)
{
    $return = NULL;
      
    if(is_array($object))
    {
        foreach($object as $key => $value)
            $return[$key] = object2array($value);
    }
    else
    {
        $var = get_object_vars($object);
          
        if($var)
        {
            foreach($var as $key => $value)
                $return[$key] = ($key && !$value) ? NULL : object2array($value);
        }
        else return $object;
    }

    return $return;
} 


function XML2Array ( $xml )
{
    $array = simplexml_load_string ( $xml );
    $newArray = array ( ) ;
    $array = ( array ) $array ;
    foreach ( $array as $key => $value )
    {
        $value = ( array ) $value ;
        $newArray [ $key] = $value [ 0 ] ;
    }
    $newArray = array_map("trim", $newArray);
  return $newArray ;
} 

class simple_xml_extended extends SimpleXMLElement
{
    public    function    Attribute($name)
    {
        foreach($this->Attributes() as $key=>$val)
        {
            if($key == $name)
                return (string)$val;
        }
    }

}

///////////////////////////////////////////////////////////////////////////////
function genera_pdf($idfactura,$ruta_pdf=NULL,$ruta_url=NULL)
{

    $idfactura=intval($idfactura);
    if($idfactura==0 )
    {
        
    }
    else
    {
        if($idfactura>0)
        {
            $sql="
            SELECT 
              multi_facturas.XML,
              `multi_facturas`.idfacturatipo
            FROM
              multi_facturas
            WHERE
              (multi_facturas.idfactura = $idfactura)    
            ";
            if(function_exists('lee_sql_mash'))
            {
                list($xml,$idfacturatipo)=mysql_fetch_row(mysql_query($sql));
            }
        }
        
    }

    $pdf=str_replace('.xml','.pdf',$xml);
    if($ruta_pdf!=NULL)
        $pdf=$ruta_pdf;
    global $masheditor;
    $urlbase=$masheditor['url'];

//echo debug_mash($masheditor);
    $rfc=$_COOKIE["RFC"];
//    $rfc=str_replace('&','',$rfc);
    $rfc=str_replace('&','---',$rfc);

    $hora=time();
    $md5=md5("mash,$rfc,$hora,");
    $authpdf="$rfc,$hora,$md5";
    $carpeta_instalacion=$masheditor['carpeta_instalacion'];
    if($carpeta_instalacion!='')
        $carpeta_instalacion="$carpeta_instalacion/";
    $url="$urlbase/mt,46,1/idfacturahtml,$idfactura/impresion,si/?authpdf=$authpdf";

    if($ruta_url!=NULL)
    {
        

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
        {
            $url=$ruta_url;
        }
        else
        {
            $url=$ruta_url;
            if(function_exists('formato_url_mash'))
            {
                $url=formato_url_mash($url);
            }
            $url="$url/?authpdf=$authpdf";        
        } 

    }
unlink("$carpeta_instalacion$pdf");

    $ruta='';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
    {
        $SO=$_SERVER['PROCESSOR_IDENTIFIER'];
        if(strpos("  $SO",'x86')>0)
        {
            //32bits
            $ruta='c:\\cfdipdf\\32\\';
        }
        else
        {
            //64 bits
            $ruta='c:\\cfdipdf\\64\\';
        }
    }

// = tiket
if($idfacturatipo==7)
{
    //ticket
    $tipo_hoja='A8';
    
}
else
{
    //factura
    $tipo_hoja='A4';
}

    if(file_exists("$carpeta_instalacion$pdf")==false)
    {
       $comando=$ruta."wkhtmltopdf  -s $tipo_hoja -B 1 -T 1 -L 1 -R 1  \"$url\"  \"$carpeta_instalacion$pdf\"    "; //   -B 1 -T 1 -L 1 -R 1 -s A4 &
    }

//echo $comando;

    $resultado=shell_exec($comando);
    $url=$masheditor['url'];
    $valor= ver_pdf("$url/$pdf");


    return $valor;
    
}
///////////////////////////////////////////////////////////////////////////////

function ver_pdf($pdf)
{   

    $hora=time();
    inicializa_jquery();
    $idrand=rand();
    $html="
    <div id='iddbme_pdf_$idrand'></div>
    <script>
        dbme_muestra_pdf($idrand,'$pdf');
    </script>
    
    ";


    return $html;
}

///////////////////////////////////////////////////////////////////////////////
function formato_metodo_pago($metodo_pago)
{
    $metodo_pago=str_replace('01','Efectivo (01)',$metodo_pago);
    $metodo_pago=str_replace('02','Cheque Nominativo (02)',$metodo_pago);
    $metodo_pago=str_replace('03','Transferencia electrónica de fondos (03)',$metodo_pago);
    $metodo_pago=str_replace('04','Tarjetas de crédito (04)',$metodo_pago);
    $metodo_pago=str_replace('05','Monederos electrónicos (05)',$metodo_pago);
    $metodo_pago=str_replace('06','Dinero electrónico (06)',$metodo_pago);
    $metodo_pago=str_replace('07','Tarjetas digitales (07)',$metodo_pago);
    $metodo_pago=str_replace('08','Vales de despensa (08)',$metodo_pago);
    $metodo_pago=str_replace('09','Bienes (09)',$metodo_pago);
    $metodo_pago=str_replace('10','Servicio (10)',$metodo_pago);
    $metodo_pago=str_replace('11','Por cuenta de tercero (11)',$metodo_pago);
    $metodo_pago=str_replace('12','Dación en pago (12)',$metodo_pago);
    $metodo_pago=str_replace('13','Pago por subrogación (13)',$metodo_pago);
    $metodo_pago=str_replace('14','Pago por consignación (14)',$metodo_pago);
    $metodo_pago=str_replace('15','Condonación (15)',$metodo_pago);
    $metodo_pago=str_replace('16','Cancelación (16)',$metodo_pago);
    $metodo_pago=str_replace('17','Compensación (17)',$metodo_pago);
    $metodo_pago=str_replace('98','NA (98)',$metodo_pago);
    $metodo_pago=str_replace('99','Otros (99)',$metodo_pago);
    $metodo_pago=str_replace('28','Tarjeta de Débito (28)',$metodo_pago);
    $metodo_pago=str_replace('29','Tarjeta de Servicio (29)',$metodo_pago);

/*

01 – Efectivo
02 – Cheque
03 – Transferencia
04 – Tarjetas de crédito
05 – Monederos electrónicos
06 – Dinero electrónico
07 – Tarjetas digitales
08 – Vales de despensa
09 – Bienes
10 – Servicio
11 – Por cuenta de tercero
12 – Dación en pago
13 – Pago por subrogación
14 – Pago por consignación
15 – Condonación
16 – Cancelación
17 – Compensación
98 – NA
99 – Otros



preuba
*/
    $metodo_pago=strtoupper($metodo_pago);
    return $metodo_pago;
}
///////////////////////////////////////////////////////////////////////////////
function formato_impuestos($impuesto)
{
    $impuesto=str_replace('001','ISR',$impuesto);
    $impuesto=str_replace('002','IVA',$impuesto);
    $impuesto=str_replace('003','IEPS',$impuesto);
    
    $impuesto=strtoupper($impuesto);
    return $impuesto;
}
////////////////////////////////////////////////////////////////////////////////
function formato_metodo_pago33($metodo_pago)
{
    $metodo_pago=str_replace('PUE','Pago en una sola exhibiciOn (PUE)',$metodo_pago);
    $metodo_pago=str_replace('PIP','Pago inicial y parcialidades (PIP)',$metodo_pago);
    $metodo_pago=str_replace('PPD','Pago en parcialidades o diferido (PPD)',$metodo_pago);

    $metodo_pago=strtoupper($metodo_pago);
    return $metodo_pago;
}
///////////////////////////////////////////////////////////////////////////////
function formato_forma_pago33($forma_pago)
{
    $forma_pago=str_replace('01','Efectivo (01)',$forma_pago);
    $forma_pago=str_replace('02','Cheque Nominativo (02)',$forma_pago);
    $forma_pago=str_replace('03','Transferencia electrónica de fondos (03)',$forma_pago);
    $forma_pago=str_replace('04','Tarjetas de crédito (04)',$forma_pago);
    $forma_pago=str_replace('05','Monederos electrónicos (05)',$forma_pago);
    $forma_pago=str_replace('06','Dinero electrónico (06)',$forma_pago);
    //$forma_pago=str_replace('07','Tarjetas digitales (07)',$forma_pago);
    $forma_pago=str_replace('08','Vales de despensa (08)',$forma_pago);
    //$forma_pago=str_replace('09','Bienes (09)',$forma_pago);
    //$forma_pago=str_replace('10','Servicio (10)',$forma_pago);
    //$forma_pago=str_replace('11','Por cuenta de tercero (11)',$forma_pago);
    $forma_pago=str_replace('12','Dación en pago (12)',$forma_pago);
    $forma_pago=str_replace('13','Pago por subrogación (13)',$forma_pago);
    $forma_pago=str_replace('14','Pago por consignación (14)',$forma_pago);
    $forma_pago=str_replace('15','Condonación (15)',$forma_pago);
    //$forma_pago=str_replace('16','Cancelación (16)',$forma_pago);
    $forma_pago=str_replace('17','Compensación (17)',$forma_pago);
    $forma_pago=str_replace('23','Novación (23)',$forma_pago);
    $forma_pago=str_replace('24','Confusión (24)',$forma_pago);
    $forma_pago=str_replace('25','Remisión de deuda (25)',$forma_pago);
    $forma_pago=str_replace('26','Prescripción o caducidad (26)',$forma_pago);
    $forma_pago=str_replace('27','A satisfacción del acreedor (27)',$forma_pago);
    $forma_pago=str_replace('28','Tarjeta de Débito (28)',$forma_pago);
    $forma_pago=str_replace('29','Tarjeta de Servicio (29)',$forma_pago);
    $forma_pago=str_replace('99','Por definir (99)',$forma_pago);
/*
01 – Efectivo
02 – Cheque
03 – Transferencia
04 – Tarjetas de crédito
05 – Monederos electrónicos
06 – Dinero electrónico
07 – Tarjetas digitales
08 – Vales de despensa
09 – Bienes
10 – Servicio
11 – Por cuenta de tercero
12 – Dación en pago
13 – Pago por subrogación
14 – Pago por consignación
15 – Condonación
16 – Cancelación
17 – Compensación
98 – NA
99 – Otros
*/
    $forma_pago=strtoupper($forma_pago);
    return $forma_pago;
}



?>
