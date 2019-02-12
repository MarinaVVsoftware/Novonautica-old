<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:59:17 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class TransactionResult extends baseObject
{

    public static function fromArray($array) {
        // Se verifica que llegue un Array
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser un array", __CLASS__, __FUNCTION__));
        }

        $result = new TransactionResult();

        foreach($array as $key => $value){
            $result->vars[$key] = $value;
        }

        return $result;
    }

    /**
     * DoTransactionResult constructor.
     * Create an Instance from static function.
     */
    protected function __construct() { }

    public function getResponseCode() {
        return $this->vars['ResponseCode'];
    }

    public function getResponseMessage() {
        return $this->vars['ResponseMessage'];
    }

    public function getTransactionId() {
        return $this->vars['TransactionId'];
    }

    public function getTransactionDate() {
        return $this->vars['TransactionDate'];
    }

    public function getProviderAuthorization() {
        return $this->vars['ProviderAuthorization'];
    }

    public function getLegalInformation() {
        return $this->vars['LegalInformation'];
    }

    public function getAditionalInfo1() {
        return $this->vars['AditionalInfo1'];
    }

    public function getAditionalInfo2() {
        return $this->vars['AditionalInfo2'];
    }

    public function getAditionalInfo3() {
        return $this->vars['AditionalInfo3'];
    }

    public function getAditionalInfo4() {
        return $this->vars['AditionalInfo4'];
    }

    public function getAditionalInfo5() {
        return $this->vars['AditionalInfo5'];
    }

    public function getAditionalInfo6() {
        return $this->vars['AditionalInfo6'];
    }
}