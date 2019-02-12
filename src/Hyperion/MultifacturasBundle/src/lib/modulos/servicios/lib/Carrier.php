<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:58:38 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class Carrier extends baseObject {

    public static function fromArray($array) {
        // Se verifica que llegue un Array
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser un array", __CLASS__, __FUNCTION__));
        }

        $carrier = new Carrier();

        foreach($array as $key => $value){
            $carrier->vars[$key] = $value;
        }

        return $carrier;
    }

    protected function __construct(){
        parent::__init(array('CarrierId', 'CarrierName'));
    }

    public function getCarrierId()
    {
        return $this->vars['CarrierId'];
    }

    public function getCarrierName()
    {
        return $this->vars['CarrierName'];
    }
}