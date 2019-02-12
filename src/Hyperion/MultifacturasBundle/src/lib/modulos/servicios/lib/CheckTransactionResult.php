<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:58:30 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class CheckTransactionResult extends TransactionResult {
    public static function fromArray($array) {
        // Se verifica que llegue un Array
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser un array", __CLASS__, __FUNCTION__));
        }

        // Se verifica que exista el elemeto 'CheckTransactionResult'
        if(!array_key_exists(__CLASS__, $array)) {
            throw new Exception(sprintf("%s::%s No se encontró el elemento %s", __CLASS__, __FUNCTION__, __CLASS__));
        }

        // Se obtiene la informacion de la respuesta
        $array = $array[__CLASS__];

        // Objeto a devolver
        $result = new CheckTransactionResult();

        // Se asignan los atributos
        foreach($array as $key => $value){
            $result->vars[$key] = $value;
        }

        // Se devuelve el objeto
        return $result;
    }

}