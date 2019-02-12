<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:59:56 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class ProductCategory extends baseObject {

    public static function fromArray($array) {
        // Se valida que sea un array
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El párametro de entrada debe ser un array.", __CLASS__, __FUNCTION__));
        }

        // Objeto a devolver
        $category = new ProductCategory();

        // Se asignan los atributos
        foreach($array as $key => $value){
            $category->vars[$key] = $value;
        }

        // Se devuelve el objeto
        return $category;
    }

    protected function __construct() { }

    public function getProductCategoryId()
    {
        return $this->vars['ProductCategoryId'];
    }

    public function getProductCategoryName()
    {
        return $this->vars['ProductCategoryName'];
    }

    public function getImageUrl()
    {
        return $this->vars['ImageUrl'];
    }

    public function getLastUpdateDate()
    {
        return $this->vars['LastUpdateDate'];
    }
}