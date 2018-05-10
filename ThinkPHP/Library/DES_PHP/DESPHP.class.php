<?php 
namespace DES_PHP; 
/** 
 *  
 * DES FOR .NET版本 
 * @author Administrator 
 * 
 */  
class DESPHP{  
     private $key='12345678';  
     private $iv="12345678"; //偏移量  
       
     private static $_self = null;
     public  static function getInstance($key, $iv=0) {
         if (null == self::$_self) {
             self::$_self = new DESPHP ($key, $iv=0);
         }
         return self::$_self;
     }
     private function __construct($key, $iv=0 ) {
 //       $this->key = $key;  
//         if( $iv == 0 ){  
//             $this->iv = $key; //默认以$key 作为 iv  
//         }else{  
//             $this->iv = $key; //mcrypt_create_iv ( mcrypt_get_block_size (MCRYPT_DES, MCRYPT_MODE_CBC), MCRYPT_DEV_RANDOM );  
//         }  
     } 
    /**
     * 加密，
     * @param unknown $str
     */
    function encrypt($str){  
    
        $size = mcrypt_get_block_size ( MCRYPT_DES, MCRYPT_MODE_CBC );
        $str = $this->pkcs5Pad ( $str, $size );
        $data=mcrypt_cbc(MCRYPT_DES, $this->key, $str, MCRYPT_ENCRYPT, $this->iv);
        return base64_encode($data);  
    }  
    /**
     * 解密
     * @param unknown $str
     * @return unknown
     */   
    function decrypt($str){  
        
        $str = base64_decode ($str);
        $str = mcrypt_cbc(MCRYPT_DES, $this->key, $str, MCRYPT_DECRYPT, $this->iv );
        $str = $this->pkcs5Unpad( $str );
        return $str;
    }  
       
    private function hex2bin($hexData){  
        $binData = "";
        for($i = 0; $i < strlen ( $hexData ); $i += 2){
            $binData .= chr(hexdec(substr($hexData, $i, 2)));
        }
        return $binData;
    }  
   
    private function pkcs5Pad($text, $blocksize) {  
        $pad = $blocksize - (strlen ( $text ) % $blocksize);
        return $text . str_repeat ( chr ( $pad ), $pad );
    }  
       
    private function pkcs5Unpad($text) {  
        $pad = ord ( $text {strlen ( $text ) - 1} );
        if ($pad > strlen ( $text ))
            return false;
        if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
            return false;
        return substr ( $text, 0, - 1 * $pad );
    }  
       
} 
