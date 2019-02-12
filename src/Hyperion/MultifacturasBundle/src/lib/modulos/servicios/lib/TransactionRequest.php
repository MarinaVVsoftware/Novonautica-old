<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:59:30 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
require_once 'Credentials.php';

class TransactionRequest
{
    private $credentials;
    private $ProductId;
    private $Amount;
    private $Reference1 = '';
    private $Reference2 = '';
    private $Reference3 = '';
    private $PosTransactionId;

    public function __construct(Credentials $credentials) {
        $this->credentials = $credentials;
    }

    public function getCredentials() {
        return $this->credentials;
    }

    public function getProductId() {
        return (int)$this->ProductId;
    }

    public function setProductId($id) {
        if(!is_int($id)) {
            throw new Exception(sprintf("%s::%s El párametro debe ser entero.", __CLASS__, __FUNCTION__));
        }
        $this->ProductId = $id;
    }

    public function getAmount() {
        return $this->Amount;
    }

    public function setAmount($amount) {
        if(!(is_int($amount) || is_double($amount))) {
            throw new Exception(sprintf("%s::%s El párametro debe ser entero con decimales.", __CLASS__, __FUNCTION__));
        }
        $this->Amount = $amount;
    }

    public function getReference1() {
        return $this->Reference1;
    }

    public function setReference1($reference) {
        if(!is_string($reference)) {
            throw new Exception(sprintf("%s::%s El párametro debe ser una cadena de caracteres.", __CLASS__, __FUNCTION__));
        }
        $this->Reference1 = $reference;
    }

    public function getReference2() {
        return $this->Reference2;
    }

    public function setReference2($reference) {
        if(!is_string($reference)) {
            throw new Exception(sprintf("%s::%s El párametro debe ser una cadena de caracteres.", __CLASS__, __FUNCTION__));
        }
        $this->Reference2 = $reference;
    }

    public function getReference3() {
        return $this->Reference3;
    }

    public function setReference3($reference) {
        if(!is_string($reference)) {
            throw new Exception(sprintf("%s::%s El párametro debe ser una cadena de caracteres.", __CLASS__, __FUNCTION__));
        }
        $this->Reference3 = $reference;
    }

    public function getPosTransactionId() {
        return (int)$this->PosTransactionId;
    }

    public function setPosTransactionId($PosTransactionId)
    {
        if(!is_int($PosTransactionId)) {
            throw new Exception(sprintf("%s::%s El párametro debe ser entero.", __CLASS__, __FUNCTION__));
        }
        $this->PosTransactionId = $PosTransactionId;
    }
}