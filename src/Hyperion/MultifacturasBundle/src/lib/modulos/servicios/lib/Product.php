<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 06:00:14 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 *
 * Product
 * Representa la información general de un producto
 */
class Product extends baseObject
{

    /**
     * Parse Product Object from DOMElement Product
     * @param DOMElement $xmlElement
     * @return Product
     */
    public static function fromXMLElement(DOMElement $xmlElement) {
        // Objeto a devolver
        $producto = new Product();

        // Se recorren los nodos que contienen la información del producto
        foreach($xmlElement->childNodes as $nodo){

            // Tag del nodo (Nombre de la propiedad del objeto)
            $tag = $nodo->nodeName;

            // Se asignan las propiedades del Producto
            if($nodo->nodeValue == 'true')
                $producto->vars[$tag] = true;
            else {
                if ($nodo->nodeValue == 'false')
                    $producto->vars[$tag] = false;
                else
                    $producto->vars[$tag] = $nodo->nodeValue;
            }
        }

        // Se devuelve el Producto
        return $producto;
    }

    protected function __construct() {
        $atr = array(
            'ProductId',
            'ProductName',
            'OrderPriority',
            'ExtraChargeEndClient',
            'Amount',
            'FixedClientCommission',
            'CategoryId',
            'LegalInformation',
            'CarrierId',
            'CarrierName',
            'Reference1',
            'LenghtRef1',
            'Reference2',
            'LenghtRef2',
            'Reference3',
            'LenghtRef3',
            'ProcessbyDistributor',
            'ProductsCommissionsOperationType',
            'Info1',
            'Info2',
            'Info3'
        );
        parent::__init($atr);
    }

    public function getProductID()
    {
        return $this->vars['ProductId'];
    }

    public function getProductName()
    {
        return $this->vars['ProductName'];
    }

    public function getOrderPriority()
    {
        return $this->vars['OrderPriority'];
    }

    public function getExtraChargeEndClient()
    {
        return $this->vars['ExtraChargeEndClient'];
    }

    public function getAmount()
    {
        return $this->vars['Amount'];
    }

    public function getFixedClientCommission()
    {
        return $this->vars['FixedClientCommission'];
    }

    public function getCategoryId()
    {
        return $this->vars['CategoryId'];
    }

    public function getLegalInformation()
    {
        return $this->vars['LegalInformation'];
    }

    public function getCarrierId()
    {
        return $this->vars['CarrierId'];
    }

    public function getCarrierName()
    {
        return $this->vars['CarrierName'];
    }

    public function getReference1()
    {
        return $this->vars['Reference1'];
    }

    public function getLenghtRef1()
    {
        return $this->vars['LenghtRef1'];
    }

    public function getReference2()
    {
        return $this->vars['Reference2'];
    }

    public function getLenghtRef2()
    {
        return $this->vars['LenghtRef2'];
    }

    public function getReference3()
    {
        return $this->vars['Reference3'];
    }

    public function getLenghtRef3()
    {
        return $this->vars['LenghtRef3'];
    }

    public function getProcessbyDistributor()
    {
        return $this->vars['ProcessbyDistributor'];
    }

    public function getProductsCommissionsOperationType()
    {
        return $this->vars['ProductsCommissionsOperationType'];
    }

    public function getInfo1()
    {
        return $this->vars['Info1'];
    }

    public function getInfo2()
    {
        return $this->vars['Info2'];
    }

    public function getInfo3()
    {
        return $this->vars['Info3'];
    }
}