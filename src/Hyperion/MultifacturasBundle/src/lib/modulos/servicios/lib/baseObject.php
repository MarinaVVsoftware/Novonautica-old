<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:58:43 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
abstract class baseObject {
    protected $vars = array();

    protected function __init($array) {
        foreach($array as $v) {
            $this->vars[$v] = null;
        }
    }

    public function ToArray() {
        return $this->vars;
    }
}