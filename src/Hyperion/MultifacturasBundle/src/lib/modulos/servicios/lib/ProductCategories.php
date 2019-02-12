<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 06:00:01 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class ProductCategories {
    public static function fromArray($array) {
        // Se valida que sea un array
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El párametro de entrada debe ser un array.", __CLASS__, __FUNCTION__));
        }

        // Se verifica que exista el elemento 'ProductCategories'
        if(!array_key_exists(__CLASS__, $array)) {
            throw new Exception(sprintf("%s::%s No se encontró el elemento '%s'.", __CLASS__, __FUNCTION__, __CLASS__));
        }

        // Arreglo a devolver
        $categories = array();

        // Se recorren los nodos hijos (Product)
        foreach($array[__CLASS__] as $categoria) {
            // Se crea el objeto PRoductCategory
            $cat = ProductCategory::fromArray($categoria);

            // Se agrega al arreglo
            array_push($categories, $cat);
        }

        // Se devuelve el arreglo de categorias de productos
        return $categories;
    }
}