<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:59:35 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class SubmitPayNotificationResult extends baseObject {

    public static function fromArray($array) {
        // Se valida que sea un arreglo
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser un array", __CLASS__, __FUNCTION__));
        }

        // Se valida que exista el elemento 'SubmitPayNotificationResult'
        if(!array_key_exists(__CLASS__, $array)) {
            throw new Exception(sprintf("%s::%s No se encontró el elemento %s", __CLASS__, __FUNCTION__, __CLASS__));
        }

        // Objeto a devolver
        $paynot = new SubmitPayNotificationResult();

        // Se asignan los atributos
        foreach($array[__CLASS__] as $key => $value){
            $paynot->vars[$key] = $value;
        }

        // Se devuelve el objeto
        return $paynot;
    }

    protected function __construct() { }

    public function getResponseCode()
    {
        return $this->ResponseCode;
    }

    public function getPaymentId()
    {
        return $this->PaymentId;
    }

    public function getResponseMessage()
    {
        return $this->ResponseMessage;
    }

    public function getOtherInfo()
    {
        return $this->OtherInfo;
    }
}