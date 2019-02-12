<?php
namespace SWServices\Cancelation;
use SWServices\Cancelation\CancelationRequest as cancelationRequest;
use Exception;
class CancelationService {
    private static $_cfdiData = null;
    private static $_url = null;
    private static $_token = null;
    private static $_xml = null;
    private static $_proxy = null;
    public function __construct($params) {
        $c = count($params);
        if($c == 7 || $c == 8)
            self::setCSD($params);
        else if ($c == 3 || $c == 4)
            self::setXml($params);
        
        else
           throw new Exception('Número de parámetros incompletos.');
    }
    public static function Set($params) {
        return new CancelationService($params);
    }
    public static function CancelationByCSD() {
        return CancelationService::sendReqCSD(self::$_url, self::$_token, self::$_cfdiData, self::$_proxy);
    }
    public static function CancelationByXML() {
        return CancelationService::sendReqXML(self::$_url, self::$_token, self::$_xml, self::$_proxy);
    }
    private static function setCSD($params) {
        if(isset($params['url']) && isset($params['token']) && isset($params['uuid']) && isset($params['password']) && isset($params['rfc']) && isset($params['b64Cer']) && isset($params['b64Key'])) {
            self::$_cfdiData = array(
                'uuid'=> $params['uuid'],
                'password'=> $params['password'],
                'rfc'=> $params['rfc'],
                'b64Cer'=> $params['b64Cer'],
                'b64Key'=> $params['b64Key']
            );
            self::$_url = $params['url'];
            self::$_token = $params['token'];
            if(isset($params['proxy'])){
                self::$_proxy = $params['proxy'];
            }
        } else {
            throw new Exception('Parámetros incompletos. Debe especificarse uuid, password, rfc, b64Cer, b64Key');
        }
    }
    private static function setXml($params) {
        if(isset($params['url']) && isset($params['token']) && isset($params['xml'])) {
            self::$_url = $params['url'];
            self::$_token = $params['token'];
            self::$_xml = $params['xml'];
            if(isset($params['proxy'])){
                self::$_proxy = $params['proxy'];
            }
        } else {
            throw new Exception('Parámetros incompletos. Debe especificarse url, token, y archivo xml');
        }
    }

     public static function sendReqCSD($url, $token, $cfdiData, $proxy) {

        $data = json_encode($cfdiData);

        $curl  = curl_init($url.'/cfdi33/cancel/csd');
       // echo $curl;
        curl_setopt($curl , CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl , CURLOPT_POST, true);
        if(isset($proxy)){
            curl_setopt($curl , CURLOPT_PROXY, $proxy);
        }
        curl_setopt($curl , CURLOPT_HTTPHEADER , array(
            'Content-Type: application/json;  ',
            'Content-Length: ' . strlen($data),
            'Authorization: Bearer '.$token
            ));  
        curl_setopt($curl , CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);
        echo "response: ".$response;
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl );
        curl_close($curl);
        if ($err) {
            throw new Exception("cURL Error #:" . $err);
        } else{
            return json_decode($response);
        }
    }


public static function sendReqXML($url, $token, $xml, $proxy){
        $delimiter = '-------------' . uniqid();
        $fileFields = array(
            'xml' => array(
                'type' => 'text/xml',
                'content' => $xml
                )
            );
        $data = '';
        // populate file fields
        foreach ($fileFields as $name => $file) {
            $data .= "--" . $delimiter . "\r\n";
            // "filename" attribute is not essential; server-side scripts may use it
            $data .= 'Content-Disposition: form-data; name="' . $name . '";' .
            ' filename="' . $name . '"' . "\r\n";
            // this is, again, informative only; good practice to include though
            $data .= 'Content-Type: ' . $file['type'] . "\r\n";
            // this endline must be here to indicate end of headers
            $data .= "\r\n";
            // the file itself (note: there's no encoding of any kind)
            $data .= $file['content'] . "\r\n";
        }
        // last delimiter
        $data .= "--" . $delimiter . "--\r\n";
        $curl  = curl_init($url.'/cfdi33/cancel/xml');
        curl_setopt($curl , CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl , CURLOPT_POST, true);
        if(isset($proxy)){
            curl_setopt($curl , CURLOPT_PROXY, $proxy);
        }
        curl_setopt($curl , CURLOPT_HTTPHEADER , array(
            'Content-Type: multipart/form-data; boundary=' . $delimiter,
            'Content-Length: ' . strlen($data),
            'Authorization: Bearer '.$token
            ));  
        curl_setopt($curl , CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl );
        curl_close($curl );
        if ($err) {
            throw new Exception("cURL Error #:" . $err);
        } else{
            return json_decode($response);
        }
    }




}
?>