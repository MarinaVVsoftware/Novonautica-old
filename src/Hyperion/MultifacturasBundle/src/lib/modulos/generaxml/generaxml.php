<?php
/**
 * La funcion siempre debe comenzar con tres guiones bajo y el nombre del mismo archivo PHP
 * SIN extension, y recibir una variable; esta variable puede tener el nombre que se desee.
 */
 //error_reporting(0);
function ___generaxml($datos)
{
    // Se extraen los datos necesarios del parametro de entrada
    
    // Se procesa la informacion recibida y/o ejecutan las operaciones pertinentes
    $resultado = 'OK';
    $doc = new DOMDocument('1.0', 'UTF-8');
    
    // Se reemplaza el nombre 'factura' por 'cfdi:Comprobante'
    $datos['cfdi:Comprobante'] = $datos['factura'];
    unset($datos['factura']);
    
    // Nodo raíz del CFDI
    $raiz = $doc->createElement('cfdi:Comprobante');
    
    // Se parsea el array
    parseArray($datos['cfdi:Comprobante'], $raiz, $doc);
    
    // Se agrega el nodo raíz
    $doc->appendChild($raiz);
    
    // Se indenta el codigo xml
    $doc->formatOutput = true;
    
    // Se guarda el archivo
    $doc->save($datos['ruta_xml']);
    
    /*
     * Siempre se debe de regresar un arreglo asociativo, es decir que los datos que se
     * pretendan devolver siempre se puedan identificar/localizar por medio de una cadena
     */
    return array('resultado' => 'OK');
}

function parseArray(array $array, DOMElement $padre, DOMDocument $doc) {
	// Se recorre el arreglo de entrada
	foreach($array as $nombre => $valor) {
		// Se valida que $val sea un arreglo
		if(is_array($valor)) {
			// Si la llave es un string
			if(is_string($nombre)) {
				$nodo = $doc->createElement($nombre);
				parseArray($valor, $nodo, $doc);
				$padre->appendChild($nodo);
			}else {
				// Si la llave es un entero
				parseArray($valor, $padre, $doc);
			}
		}else {
			$padre->setAttribute($nombre, $valor);
		}
	}
}
