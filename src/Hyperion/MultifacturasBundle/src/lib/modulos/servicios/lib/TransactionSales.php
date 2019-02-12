<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:59:03 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class TransactionSales {
    protected $TransactionId;
    protected $ClientId;
    protected $PosId;
    protected $TransactionDate;
    protected $ProductName;
    protected $CarrierName;
    protected $Amount;
    protected $Authorization;
    protected $ResponseCode;
    protected $InternalResponseMessage;
    protected $POSTransactionId;
    protected $Reference1;
    protected $Reference2;
    protected $Reference3;
    protected $StoreName;
    protected $CashierFirsName;
    protected $CashierLastName;

    public static function fromXMLElement(DOMElement $xmlElement) {
        // Objeto a devolver
        $transaction = new TransactionSales();

        // Se recorren los nodos que contienen la información de la transacción
        foreach($xmlElement->childNodes as $nodo){

            // Tag del nodo (Nombre de la propiedad del objeto)
            $tag = $nodo->nodeName;

            // Se asignan las propiedades de la transacción
            $transaction->$tag = $nodo->nodeValue;
        }

        // Se devuelve la transacción
        return $transaction;
    }

    protected function __construct() { }
}