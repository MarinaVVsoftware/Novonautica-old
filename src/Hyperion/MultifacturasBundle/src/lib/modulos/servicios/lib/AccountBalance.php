<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:58:58 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class AccountBalance extends baseObject {

    protected function __construct() { }

    public static function fromXMLElement(DOMElement $xmlElement) {
        // Objeto a devolver
        $balance = new AccountBalance();

        // Se recorren los nodos que contienen la información del producto
        foreach($xmlElement->childNodes as $nodo){

            // Tag del nodo (Nombre de la propiedad del objeto)
            $tag = $nodo->nodeName;

            // Se asignan las propiedades del Producto
            $balance->vars[$tag] = $nodo->nodeValue;
        }

        // Se devuelve el Producto
        return $balance;
    }

    public function getProviderName()
    {
        return $this->vars['ProviderName'];
    }

    public function getBalance()
    {
        return (int)$this->vars['Balance'];
    }

    public function getCarrierId()
    {
        return $this->vars['CarrierId'];
    }
}