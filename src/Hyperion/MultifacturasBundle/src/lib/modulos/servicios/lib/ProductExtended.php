<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:59:50 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
/**
 * ProductExtended
 * Representa la información detallada de un producto
 */
class ProductExtended extends Product {

    public static function fromArray($array) {
        /*
         * array (size=1)
          'ProductExtended' =>
            array (size=95)
              0 =>
                array (size=35)
                  ...
              1 =>
                array (size=37)
                  ...
         */

        // Se verifica que llegue un Array
        if(!is_array($array)) {
            throw new Exception(sprintf("%s::%s El parámetro debe ser un array", __CLASS__, __FUNCTION__));
        }

        $productE = new ProductExtended();

        foreach($array as $key => $value){
            //$productE->$key = $value;
            // Se asignan las propiedades del Producto
            if($value == 'true')
                $productE->vars[$key] = true;
            else {
                if ($value == 'false')
                    $productE->vars[$key] = false;
                else {
                    $productE->vars[$key] = $value;
                }
            }
        }

        return $productE;
    }

    protected function __construct() {
        parent::__construct();
        $ar = array(
            'RegexRef1',
            'RegexRef2',
            'RegexRef3',
            'ValidateRef1',
            'ValidateRef2',
            'ValidateRef3',
            'ValidateCharRef1',
            'ValidateCharRef2',
            'ValidateCharRef3',
            'ExtraInfoLabel1',
            'ExtraInfoContent1',
            'ExtraInfoLabel2',
            'ExtraInfoContent2',
            'ExtraInfoLabel3',
            'ExtraInfoContent3',
            'ExtraInfoLabel4',
            'ExtraInfoContent4',
            'ExtraInfoLabel5',
            'ExtraInfoContent5',
            'FixAmount',
            'ClientUtility',
            'ProductInfo',
            'ProductImageUrl',
            'Include_Tax',
            'IsBillable',
            'IsRePrintable',
            'LastUpdatedDate',
            'TotalPaymentVisible'
        );
        parent::__init($ar);
    }

    public function getRegexRef1()
    {
        return $this->RegexRef1;
    }

    public function getRegexRef2()
    {
        return $this->RegexRef2;
    }

    public function getRegexRef3()
    {
        return $this->RegexRef3;
    }

    public function getValidateRef1()
    {
        return $this->ValidateRef1;
    }

    public function getValidateRef2()
    {
        return $this->ValidateRef2;
    }

    public function getValidateRef3()
    {
        return $this->ValidateRef3;
    }

    public function getValidateCharRef1()
    {
        return $this->ValidateCharRef1;
    }

    public function getValidateCharRef2()
    {
        return $this->ValidateCharRef2;
    }

    public function getValidateCharRef3()
    {
        return $this->ValidateCharRef3;
    }

    public function getExtraInfoLabel1()
    {
        return $this->ExtraInfoLabel1;
    }

    public function getExtraInfoContent1()
    {
        return $this->ExtraInfoContent1;
    }

    public function getExtraInfoLabel2()
    {
        return $this->ExtraInfoLabel2;
    }

    public function getExtraInfoContent2()
    {
        return $this->ExtraInfoContent2;
    }

    public function getExtraInfoLabel3()
    {
        return $this->ExtraInfoLabel3;
    }

    public function getExtraInfoContent3()
    {
        return $this->ExtraInfoContent3;
    }

    public function getExtraInfoLabel4()
    {
        return $this->ExtraInfoLabel4;
    }

    public function getExtraInfoContent4()
    {
        return $this->ExtraInfoContent4;
    }

    public function getExtraInfoLabel5()
    {
        return $this->ExtraInfoLabel5;
    }

    public function getExtraInfoContent5()
    {
        return $this->ExtraInfoContent5;
    }

    public function getFixAmount()
    {
        return $this->FixAmount;
    }

    public function getClientUtility()
    {
        return $this->ClientUtility;
    }

    public function getProductInfo()
    {
        return $this->ProductInfo;
    }

    public function getProductImageUrl()
    {
        return $this->ProductImageUrl;
    }

    public function getIncludeTax()
    {
        return $this->Include_Tax;
    }

    public function getIsBillable()
    {
        return $this->IsBillable;
    }

    public function getIsRePrintable()
    {
        return $this->IsRePrintable;
    }

    public function getLastUpdatedDate()
    {
        return $this->LastUpdatedDate;
    }

    public function getTotalPaymentVisible()
    {
        return $this->TotalPaymentVisible;
    }
}