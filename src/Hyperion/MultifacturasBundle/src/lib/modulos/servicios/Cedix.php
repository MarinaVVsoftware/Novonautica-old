<?php

/**
 * Created by PhpStorm.
 * Author: Isaac Robles García
 * Date: 12/11/2015
 * Time: 02:40 PM
 */

class Cedix {
    private $servicio;

    /**
     * Cedix constructor.
     * @param $clientId int ID de Cliente
     * @param $storeId int ID de Tienda
     * @param $posId int ID del POS
     * @param $clerkCode string Código de Cajero
     * @param $urlWS string URL del Web Service
     */
    public function __construct($clientId, $storeId, $posId, $clerkCode, $urlWS) {
        // Función para auto importar clases
        spl_autoload_register(function ($class) {
            require_once 'lib' . DIRECTORY_SEPARATOR . $class . '.php';
        });
        // Se crean las credenciales
        $credenciales = new Credentials($clientId, $storeId, $posId, $clerkCode);
        // Se crea el servicio
        $this->servicio = new Service($credenciales, $urlWS);
    }

    public function GetCredentials() {
        return $this->servicio->getCredentials();
    }

    /**
     * @param int $idProducto
     * @param int $monto
     * @param $idTransaccion
     * @param string $referencia1
     * @param string $referencia2
     * @param string $referencia3
     * @return TransactionResult
     * @throws Exception
     */
    public function TransaccionGenerica($idProducto, $monto = 0, $idTransaccion, $referencia1 = null, $referencia2 = null, $referencia3 = null) {
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());

        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        if($referencia1 != null)
            $transactionRequest->setReference1($referencia1);
        else
            $transactionRequest->setReference1('');
        if($referencia2 != null)
            $transactionRequest->setReference2($referencia2);
        else
            $transactionRequest->setReference2('');
        if($referencia3 != null)
            $transactionRequest->setReference3($referencia3);
        else
            $transactionRequest->setReference3('');
            
        $transactionRequest->setPosTransactionId($idTransaccion);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------+
     *                                           FUNCIONES DEL WEB SERVICE                                             |
     * ----------------------------------------------------------------------------------------------------------------+
     */

    /**
     * @return array(Carrier)
     * @throws Exception
     */
    public function ListaProveedores() {
        return $this->servicio->GetAvailableCarriers();
    }

    public function ListaProveedoresPorCategoria($categoria) {
        return $this->servicio->GetAvailableCarriersbyCategory($categoria);
    }

    public function ListaCategorias() {
        return $this->servicio->GetProductCategory();
    }

    /**
     * @param $idProveedor int
     * @return array(Product)
     * @throws Exception
     */
    public function ListaProductosPorProveedor($idProveedor) {
        return $this->servicio->GetProductsByCarrier($idProveedor);
    }

    /**
     * @return array(Product)
     * @throws Exception
     */
    public function ListaCompletaProductos() {
        return $this->servicio->GetAllProductList();
    }

    /**
     * @return array(ProductExtended)
     * @throws Exception
     */
    public function ListaDetalladaProductos() {
        return $this->servicio->GetAllProductExtendList();
    }

    /**
     * @return array(string)
     * @throws Exception
     */
    public function ListaDeBancos() {
        return $this->servicio->GetAvailableBanks();
    }

    /**
     * @return array(string)
     * @throws Exception
     */
    public function ListaMetodosPago() {
        return $this->servicio->GetAvailablePaymentMethods();
    }

    public function VerificaCredenciales() {
        return $this->servicio->ValidateCredentials();
    }

    /**
     * @return array(AccountBalance)
     * @throws Exception
     */
    public function SaldosDeProveedores() {
        return $this->servicio->GetAllBalances();
    }

    /**
     * @param $idProveedor
     * @return AccountBalance
     * @throws Exception
     */
    public function SaldoPorProveedor($idProveedor) {
        return $this->servicio->GetBalancebyCarrier($idProveedor);
    }

    public function InformacionFTP() {
        return $this->servicio->GetFTPReconciliationInfo();
    }

    /**
     * @param $monto double
     * @param $banco string
     * @param $documento string
     * @param $fecha DateTime
     * @param $cuenta string
     * @param $formaPago string
     * @param $nombreProveedor string
     * @return SubmitPayNotificationResult
     * @throws Exception
     */
    public function EnviaNotificacionDePago($monto, $banco, $documento, $fecha, $cuenta, $formaPago, $nombreProveedor) {
        return $this->servicio->SubmitPayNotification($monto, $banco, $documento, $fecha, $cuenta, $formaPago, $nombreProveedor);
    }

    /**
     * Devuelve un arreglo con el nombre del archivo SIN extension y el contenido del archivo en base64
     * @param $transacciones
     * @return array(nombreArchivo, contenido)
     */
    public function CadenaArchivoConciliacion($transacciones) {
        // Se inicializa la cadena (Evita Warning)
        $cadena = '';

        // Se recorre cada subarreglo
        foreach($transacciones as $transaccion) {
            // Se recorre cada elemento del subarreglo
            foreach($transaccion as $dato) {
                // Se agrega el campo con su respectiva coma
                $cadena .= "$dato,";
            }
            // Se elimina la ultima coma y se agrega el salto de linea
            $cadena = substr($cadena, 0, strlen($cadena) - 1) . PHP_EOL;
        }

        // Se retorna un arreglo con dos string
        return array(
            // Bombre del archivo SIN extensión
            $this->servicio->getCredentials()->getClientId() . '_' . date('dmY'),
            // Contenido del archivo en base64
            base64_encode($cadena)
        );
    }
    
    public function ConsultaTransaccion($clientId, $storeId, $posId, $clerkCode, $prodId, $monto, $ref1, $ref2, $ref3, $posTrId) {
        $credenciales = new Credentials($clientId, $storeId, $posId, $clerkCode);
        $request = new TransactionRequest($credenciales);
        $request->setProductId((int)$prodId);
        $request->setAmount((double)$monto);
        $request->setReference1($ref1);
        $request->setReference2($ref2);
        $request->setReference3($ref3);
        $request->setPosTransactionId((int)$posTrId);
        return $this->servicio->CheckTransaction($request);
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------+
     *                                           RECARGAS DE TIEMPO AIRE                                               |
     * ----------------------------------------------------------------------------------------------------------------+
     */

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function RecargaTelcel($idProducto, $monto, $numTelefono, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function RecargaMovistar($idProducto, $monto, $numTelefono, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function RecargaCierto($idProducto, $monto, $numTelefono, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function RecargaVirginMobile($idProducto, $monto, $numTelefono, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function RecargaALO($idProducto, $monto, $numTelefono, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function RecargaMasRecarga($idProducto, $monto, $numTelefono, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function RecargaMazTiempo($idProducto, $monto, $numTelefono, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function RecargaIusacell($idProducto, $monto, $numTelefono, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function RecargaUnefon($idProducto, $monto, $numTelefono, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function RecargaNextel($idProducto, $monto, $numTelefono, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------+
     *                                           TARJETAS DE REGALO                                                    |
     * ----------------------------------------------------------------------------------------------------------------+
     */

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaITUNES($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaSKYPE($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaSKYPE_DDP($idProducto, $monto, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setPosTransactionId($idTransaction);
        
        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaFacturaFiel($idProducto, $monto, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setPosTransactionId($idTransaction);
        
        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaAmericanista($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaSubscripcionDish($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaClubPenguin($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaNintendo($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaKaspersky1PC($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaPlayStationSEN($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaFacebook($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaCinepolis($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaAndaleLD($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaBajalibros($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaSonyPlus($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaPlayStation($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $telefono1 string
     * @param $telefono2 string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaXBox($idProducto, $monto, $telefono1, $telefono2, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($telefono1);
        $transactionRequest->setReference2($telefono2);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function TarjetaSaludo($idProducto, $monto, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------+
     *                                           RECARGAS DE TELEVIA                                                   |
     * ----------------------------------------------------------------------------------------------------------------+
     */

    public function RecargaTelevia($idProducto, $monto, $referencia, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($referencia);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------+
     *                                           PAGOS DE SERVICIOS                                                    |
     * ----------------------------------------------------------------------------------------------------------------+
     */

    /**
     * @param $idProducto int
     * @param $monto double
     * @param $numTelefono string
     * @param $digitoVerificador string
     * @param $idTransaction int
     * @return TransactionResult
     * @throws Exception
     */
    public function  PagoTelmex($idProducto, $monto, $numTelefono, $digitoVerificador, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($numTelefono);
        $transactionRequest->setReference3($digitoVerificador);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoCFE($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoSKY($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoMEGACABLE($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoDISH($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoGAS($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoInfonavit($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoMAXCOM($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoAdospaco($idProducto, $monto, $cuenta, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($cuenta);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoAgura_Drenaje_MTY($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoMultimediosMTY($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoAguakan($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoFomerrey($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoGIGACABLE($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }

    public function PagoTelnor($idProducto, $monto, $codigoBarras, $idTransaction) {
        // Se crea la transacción
        $transactionRequest = new TransactionRequest($this->servicio->getCredentials());
        $transactionRequest->setProductId($idProducto);
        $transactionRequest->setAmount($monto);
        $transactionRequest->setReference1($codigoBarras);
        $transactionRequest->setPosTransactionId($idTransaction);

        return $this->servicio->DoTransaction($transactionRequest);
    }
}

/* Credenciales a utilizar
$credenciales = new Credentials(436431, 1, 64282, '261015');
//436431, 1, 64282, "26101");

// Objeto para manejar el servicio
$servicio = new Service($credenciales);*/

//var_dump($servicio->GetAllProductList());
//var_dump($servicio->GetAllProductExtendList());
//var_dump($servicio->GetAvailableCarriers());
//var_dump($servicio->GetProductsbyCarrier());
//var_dump($servicio->GetProductsbyCategoryId(1));
//var_dump($servicio->ValidateCredentials());
//var_dump($servicio->GetTransactionFromPeriod(new DateTime('2015-01-29'), new DateTime('2015-02-29'), 1));
//var_dump($servicio->ChangeClerkCode("123456789"));
//var_dump($servicio->GetAllBalances());
//var_dump($servicio->GetBalancebyCarrierId(1));
//var_dump($servicio->GetBalancebyCarrier(1)); ** ALIAS **
//var_dump($servicio->GetProductCategory());
//var_dump($servicio->GetAvailableBanks());
//var_dump($servicio->GetAvailablePaymentMethods());

// CASO DE PRUEBA 1.1
/* Envío de transacciones
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(31);
$transactionRequest->setAmount(20);
$transactionRequest->setReference1('8711335454');
$transactionRequest->setPosTransactionId(1);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 1.2
/* Envío de transacciones
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(32);
$transactionRequest->setAmount(30);
$transactionRequest->setReference1('8718973686');
$transactionRequest->setPosTransactionId(2);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 2
/* Envío de transacción con número inválido
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(34);
$transactionRequest->setAmount(100);
$transactionRequest->setReference1('8711581613');
$transactionRequest->setPosTransactionId(5);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 3
/* Envío de transacción cuando el saldo no es suficiente
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(38);
$transactionRequest->setAmount(500);
$transactionRequest->setReference1('8712115887');
$transactionRequest->setPosTransactionId(10);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 4
/*Envío de transacción cuando el id cliente, id sucursal, id de caja o clave de cajero son inválidos
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(34);
$transactionRequest->setAmount(100);
$transactionRequest->setReference1('8712115887');
$transactionRequest->setPosTransactionId(6);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 5
/* Envío de transacción con monto de producto inválido
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(33);
$transactionRequest->setAmount(100);
$transactionRequest->setReference1('8712115886');
$transactionRequest->setPosTransactionId(7);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 6.1 y 6.2
/* Envío de transacción con el mismo external transaction id y mismas referencias
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(39);
$transactionRequest->setAmount(50);
$transactionRequest->setReference1('9871213120');
$transactionRequest->setPosTransactionId(7);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 7.1
/* Envío de transacción con el mismo external transaction id y diferentes referencias
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(39);
$transactionRequest->setAmount(50);
$transactionRequest->setReference1('8711588933');
$transactionRequest->setPosTransactionId(8);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 7.2
/* Envío de transacción con el mismo external transaction id y diferentes referencias
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(39);
$transactionRequest->setAmount(50);
$transactionRequest->setReference1('8711588935');
$transactionRequest->setPosTransactionId(9);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 8
/* Envío de transacción retrasando la respuesta 30 segundos, luego ejecutar CheckTransaction
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(35);
$transactionRequest->setAmount(120);
$transactionRequest->setReference1('8712627111');
$transactionRequest->setPosTransactionId(100);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 9
/* Envío de transacción retrasando la respuesta 60 segundos, luego ejecutar
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(31);
$transactionRequest->setAmount(20);
$transactionRequest->setReference1('8712627112');
$transactionRequest->setPosTransactionId(110);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 10
/* Envío de transacción retrasando la respuesta 60 segundos, luego ejecutar
$transactionRequest = new TransactionRequest($credenciales);
$transactionRequest->setProductId(31);
$transactionRequest->setAmount(20);
$transactionRequest->setReference1('8711850231');
$transactionRequest->setPosTransactionId(130);
var_dump($servicio->DoTransaction($transactionRequest));*/

// CASO DE PRUEBA 11
// Envío de una Notificación de Pago
//var_dump($servicio->SubmitPayNotification(1500, "", "", new DateTime('2015-11-11'), "", "", ""));
//var_dump($servicio->SubmitPayNotification(1500, "Banamex", "A0001", new DateTime('2015-11-10'), "1234", "Efectivo", "Telcel"));