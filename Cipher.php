<?php
/**
 * Classe astratta per algoritmi di cifratura
 */
abstract class Cipher {
    protected $message;
    
    public function __construct($message = '') {
        $this->message = $message;
    }
    
    public function setMessage($message) {
        $this->message = $message;
    }
    
    abstract public function encrypt();
    abstract public function decrypt();
}

/**
 * Classe per l'algoritmo ROT13
 */
class Rot13Cipher extends Cipher {
    
    public function encrypt() {
        return str_rot13($this->message);
    }
    
    public function decrypt() {
        return str_rot13($this->message);
    }
}

/**
 * Classe per l'algoritmo Vigenère
 */
class VigenereCipher extends Cipher {
    private $key;
    
    public function __construct($message = '', $key = '') {
        parent::__construct($message);
        $this->key = strtoupper($key);
    }
    
    public function setKey($key) {
        $this->key = strtoupper($key);
    }
    
    public function encrypt() {
        if (empty($this->key)) {
            throw new Exception("Chiave non impostata");
        }
        return $this->processVigenere(true);
    }
    
    public function decrypt() {
        if (empty($this->key)) {
            throw new Exception("Chiave non impostata");
        }
        return $this->processVigenere(false);
    }
    
    private function processVigenere($encrypt) {
        $result = '';
        $keyLength = strlen($this->key);
        $keyIndex = 0;
        
        for ($i = 0; $i < strlen($this->message); $i++) {
            $char = $this->message[$i];
            
            if (ctype_alpha($char)) {
                $isUpper = ctype_upper($char);
                $char = strtoupper($char);
                
                $keyChar = $this->key[$keyIndex % $keyLength];
                $shift = ord($keyChar) - ord('A');
                
                if (!$encrypt) {
                    $shift = -$shift;
                }
                
                $charValue = ord($char) - ord('A');
                $newCharValue = ($charValue + $shift + 26) % 26;
                $newChar = chr($newCharValue + ord('A'));
                
                $result .= $isUpper ? $newChar : strtolower($newChar);
                $keyIndex++;
            } else {
                $result .= $char;
            }
        }
        
        return $result;
    }
}

/**
 * Factory per creare l'algoritmo di cifratura
 */
class CipherFactory {
    public static function create($algorithm, $message, $key = '') {
        switch ($algorithm) {
            case 'rot13':
                return new Rot13Cipher($message);
            case 'vigenere':
                return new VigenereCipher($message, $key);
            default:
                throw new Exception("Algoritmo non riconosciuto");
        }
    }
}
?>