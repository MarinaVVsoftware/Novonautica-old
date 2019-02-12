<?php

function mf_debug($datos=null)
{
    global $__mf_constantes__;
    global $__mf_debug_trace__;
    global $__mf_respuesta_modulos__;

    // URL Web Service
	$urlDebug = 'http://panel.multifacturas.com/panel/debug/wsdebug.php?wsdl';

    // Se convierte el arreglo a JSON
    $json = json_encode($__mf_debug_trace__);

    // Se carga nuSOAP
    mf_carga_libreria($__mf_constantes__['__MF_LIBS_DIR__'] . 'nusoap/nusoap.php');

    // Parametros
    $params = array(
        'debug' => $json,
		'usuario' => $datos['PAC']['usuario']
    );

	// Se crea el cliente SOAP
    $cliente = new nusoap_client($urlDebug);
    
	// Se ejecuta la funcion de debug
    $resultado = $cliente->call('RegistroDebug',$params);
	$resultado = str_replace('ID: ', $params['usuario'] . '-', $resultado);
	
	// Ruta para el archivo debug
	$rutaDebug = $__mf_constantes__['__MF_SDK_TMP__'] . time() . '.debug';
	
	// Archivo debug para codigos de error 2, 3, 4, 7, 8
	if(isset($__mf_debug_trace__['entrada']))
	{
		$coderr_debug = array(2, 3, 4, 7, 8);
		$resdk = $__mf_debug_trace__['salida'];
		if(isset($resdk['codigo_mf_numero']) && in_array((int)$resdk['codigo_mf_numero'], $resdk))
		{
			// Se guarda el archivo debug
			file_put_contents($rutaDebug, json_encode($params)."\r\n", FILE_APPEND);
		}
	}
	
	// Se agrega la respuesta al arreglo
    $__mf_respuesta_modulos__['debug'] = $resultado;
}