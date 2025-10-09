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
        // ROT13 è simmetrico: cifrare e decifrare sono la stessa operazione
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
            throw new Exception("Chiave non impostata per Vigenère");
        }
        
        return $this->processVigenere(true);
    }
    
    public function decrypt() {
        if (empty($this->key)) {
            throw new Exception("Chiave non impostata per Vigenère");
        }
        
        return $this->processVigenere(false);
    }
    
    private function processVigenere($encrypt) {
        $result = '';
        $keyLength = strlen($this->key);
        $keyIndex = 0;
        
        for ($i = 0; $i < strlen($this->message); $i++) {
            $char = $this->message[$i];
            
            // Verifica se è una lettera
            if (ctype_alpha($char)) {
                $isUpper = ctype_upper($char);
                $char = strtoupper($char);
                
                // Ottieni il valore della chiave (A=0, B=1, ..., Z=25)
                $keyChar = $this->key[$keyIndex % $keyLength];
                $shift = ord($keyChar) - ord('A');
                
                if (!$encrypt) {
                    $shift = -$shift; // Per decifrare, shifta nella direzione opposta
                }
                
                // Applica lo shift
                $charValue = ord($char) - ord('A');
                $newCharValue = ($charValue + $shift + 26) % 26;
                $newChar = chr($newCharValue + ord('A'));
                
                // Mantieni il caso originale
                $result .= $isUpper ? $newChar : strtolower($newChar);
                
                $keyIndex++;
            } else {
                // Non è una lettera, mantieni il carattere invariato
                $result .= $char;
            }
        }
        
        return $result;
    }
}

/**
 * Factory per creare l'algoritmo di cifratura appropriato
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