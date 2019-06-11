<?php

namespace Fluxuser\View\Helper;
use Zend\View\Helper\AbstractHelper;

// constants
//define('SECRET_KEY2', 'qmQ3x4eE$m3Gxgj7'); // salt

class EncryptionHelper extends AbstractHelper{
 
    public function __invoke($name, $key){
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_string = urlencode(mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $name, MCRYPT_MODE_ECB, $iv));
        return $encrypted_string;
    }
    
}