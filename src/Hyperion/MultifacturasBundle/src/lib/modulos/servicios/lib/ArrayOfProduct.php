<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:58:47 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class ArrayOfProduct
{
    /**
     * Parse Array Of Products from Xml Response
     * @param $xmlResponse
     * @return array(Product)
     * @throws Exception
     */
    public static function fromXML($xmlResponse) {
        // La respuesta llega como un array
        // array('GetAllProductList' => <XML>)
        $xmlResponse = array_pop($xmlResponse);

        // Se valida que sea un string (Código XML)
        if(!is_string($xmlResponse)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser del tipo string.", __CLASS__, __FUNCTION__));
        }

        // Se cambian las entidades HTML
        $xmlResponse = html_entity_decode($xmlResponse);

        // Se convierte a UTF-8
        $xmlResponse = utf8_encode($xmlResponse);

        // Se crea el documento apartir del XML
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xmlResponse);

        // Se verifica el Nodo raíz del documento
        if($doc->documentElement->nodeName != __CLASS__) {
            throw new Exception(sprintf("%s::%s Error al procesar XML", __CLASS__, __FUNCTION__));
        }

        // Arreglo de productos
        $products = array();

        // Se recorren los nodos hijos (Product)
        foreach($doc->documentElement->childNodes as $nodo) {
            if($nodo->nodeType == XML_ELEMENT_NODE) {

                // Se crea el producto
                $product = Product::fromXMLElement($nodo);

                // Se agrega al arreglo
                array_push($products, $product);
            }
        }

        // Se devuelve el arreglo de productos
        return $products;
    }
}