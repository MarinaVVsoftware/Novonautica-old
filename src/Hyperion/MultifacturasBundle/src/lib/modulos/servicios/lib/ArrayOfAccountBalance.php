<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:58:52 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class ArrayOfAccountBalance {
    public static function fromXML($xmlResponse) {
        // Se valida que sea un string
        if(!is_string($xmlResponse)) {
            throw new Exception(sprintf("%s::%s El parámetro de entrada debe ser una cadena de caracteres.", __CLASS__, __FUNCTION__));
        }

        // Se cambian las entidades HTML
        $xmlResponse = html_entity_decode($xmlResponse);

        // Se convierte a UTF-8
        $xmlResponse = utf8_encode($xmlResponse);

        // Se crea el documento XML
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xmlResponse);

        // Se verifica el Nodo raíz del documento
        if($doc->documentElement->nodeName != __CLASS__) {
            throw new Exception(sprintf("%s::%s Error al procesar XML", __CLASS__, __FUNCTION__));
        }

        // Arreglo de AccountBalace
        $balances = array();

        // Se recorren los nodos hijos (Product)
        foreach($doc->documentElement->childNodes as $nodo) {
            if($nodo->nodeType == XML_ELEMENT_NODE) {

                // Se crea el producto
                $balance = AccountBalance::fromXMLElement($nodo);

                // Se agrega al arreglo
                array_push($balances, $balance);
            }
        }

        // Se devuelve el arreglo de productos
        return $balances;
    }
}