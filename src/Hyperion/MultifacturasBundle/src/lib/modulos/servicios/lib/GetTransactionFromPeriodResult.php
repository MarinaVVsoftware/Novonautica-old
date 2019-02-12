<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 06:00:20 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class GetTransactionFromPeriodResult {
    public static function fromArray($array) {
        // Se valida que sea un arreglo
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El parámetro de entrada debe ser un array.", __CLASS__, __FUNCTION__));
        }

        // Se verifica que exista el elemento 'GetTransactionFromPeriodResult'
        if(!array_key_exists(__CLASS__, $array)) {
            throw new Exception(sprintf("%s::%s No se encontro el elemento '%s'.", __CLASS__, __FUNCTION__, __CLASS__));
        }

        // Se extrae el código XML
        $xmlResponse = $array[__CLASS__];

        // Se verifica que contenga algo la respuesta
        if(empty($xmlResponse)) {
           return array();
        }

        // Se valida que sea un string (Código XML)
        if(!is_string($xmlResponse)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser del tipo string.", __CLASS__, __FUNCTION__));
        }

        // Se convierte a UTF-8
        $xmlResponse = utf8_encode($xmlResponse);

        // Se crea el documento apartir del XML
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xmlResponse);

        // Se verifica el Nodo raíz del documento
        if($doc->documentElement->nodeName != __CLASS__) {
            throw new Exception(sprintf("%s::%s Error al procesar XML", __CLASS__, __FUNCTION__));
        }

        // Arreglo de Transacciones de Venta
        $transactions = array();

        // Se recorren los nodos hijos (Product)
        foreach($doc->documentElement->childNodes as $nodo) {
            if($nodo->nodeType == XML_ELEMENT_NODE) {

                // Se crea el producto
                $transaction = Product::fromXMLElement($nodo);

                // Se agrega al arreglo
                array_push($transactions, $transaction);
            }
        }

        // Se devuelven las transacciones
        return $transactions;
    }
}