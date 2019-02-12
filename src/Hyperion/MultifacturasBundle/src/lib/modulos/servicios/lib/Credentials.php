<?php
// <!-- phpDesigner :: Timestamp [11/03/2016 05:58:19 p. m.] -->
/**
 * MultiFacturas.com 2016
 * Author: I.R.G.
 */
class Credentials implements ServiceRequest{
    private $ClientId;  // int
    private $StoreId;   // int
    private $PosId;     // int
    private $ClerkCode; // string

    public function __construct($clientId, $storeId, $posId, $clerkCode) {
        $this->ClientId = $clientId;
        $this->StoreId = $storeId;
        $this->PosId = $posId;
        $this->ClerkCode = $clerkCode;
    }
    
    public function getClientId() {
        return $this->ClientId;
    }
    
    public function getStoreId() {
        return $this->StoreId;
    }
    
    public function getPosId() {
        return $this->PosId;
    }
    
    public function getClerkCode() {
        return $this->ClerkCode;
    }

    public function setClerkCode($ClerkCode)
    {
        $this->ClerkCode = $ClerkCode;
    }

    public function getParams()
    {
        return array("ClientId" => $this->ClientId, "StoreId" => $this->StoreId, "PosId" => $this->PosId, "ClerckCode" => $this->ClerkCode);
    }
}
