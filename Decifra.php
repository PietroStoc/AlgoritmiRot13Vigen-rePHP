<?php
session_start();
require_once 'Cipher.php';

$decrypted = null;
$error = null;

// Se è una richiesta POST, decifra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['encrypted_message'] ?? '';
    $algorithm = $_POST['algorithm'] ?? '';
    $key = $_POST['key'] ?? '';
    
    // Validazione
    if (empty($message)) {
        $error = 'Il messaggio cifrato non può essere vuoto';
    } elseif (!in_array($algorithm, ['rot13', 'vigenere'])) {
        $error = 'Algoritmo non valido';
    } elseif ($algorithm === 'vigenere' && empty($key)) {
        $error = 'La chiave Vigenère è obbligatoria';
    } elseif ($algorithm === 'vigenere' && !ctype_alpha($key)) {
        $error = 'La chiave deve contenere solo lettere';
    } else {
        try {
            $cipher = CipherFactory::create($algorithm, $message, $key);
            $decrypted = $cipher->decrypt();
            
            // Salva in sessione
            $_SESSION['decrypted_message'] = $decrypted;
        } catch (Exception $e) {
            $error = 'Errore durante la decifratura: ' . $e->getMessage();
        }
    }
} else {
    // Carica i dati dalla sessione se disponibili
    $message = $_SESSION['encrypted_message'] ?? '';
    $algorithm = $_SESSION['cipher_algorithm'] ?? 'rot13';
    $key = $_SESSION['cipher_key'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decifra Messaggio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container">
        <h1>Decifratura Messaggio</h1>
        <p class="subtitle">Inserisci un messaggio cifrato per decifrarlo</p>
        
        <?php if ($error): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form action="decifra.php" method="POST" class="cipher-form">
            <div class="form-group">
                <label for="encrypted_message">Messaggio Cifrato:</label>
                <textarea 
                    id="encrypted_message" 
                    name="encrypted_message" 
                    rows="5" 
                    required
                    placeholder="Inserisci il messaggio da decifrare..."
                ><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Algoritmo utilizzato:</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input 
                            type="radio" 
                            name="algorithm" 
                            value="rot13" 
                            <?php echo ($algorithm === 'rot13') ? 'checked' : ''; ?>
                            onchange="toggleKeyField()"
                        >
                        <div>
                            <strong>ROT13</strong>
                        </div>
                    </label>
                    
                    <label class="radio-label">
                        <input 
                            type="radio" 
                            name="algorithm" 
                            value="vigenere"
                            <?php echo ($algorithm === 'vigenere') ? 'checked' : ''; ?>
                            onchange="toggleKeyField()"
                        >
                        <div>
                            <strong>Vigenère</strong>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="form-group" id="key-group" style="display: <?php echo ($algorithm === 'vigenere') ? 'block' : 'none'; ?>;">
                <label for="key">Chiave Vigenère:</label>
                <input 
                    type="text" 
                    id="key" 
                    name="key" 
                    placeholder="Inserisci la chiave usata per cifrare"
                    pattern="[A-Za-z]+"
                    title="La chiave deve contenere solo lettere"
                    value="<?php echo htmlspecialchars($key); ?>"
                >
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Decifra Messaggio</button>
                <button type="reset" class="btn btn-secondary">Cancella</button>
            </div>
        </form>
        
        <?php if ($decrypted !== null): ?>
            <div class="result-box success">
                <h2>Messaggio Decifrato</h2>
                
                <div class="message-section">
                    <h3>Messaggio Cifrato:</h3>
                    <div class="message-content encrypted">
                        <?php echo nl2br(htmlspecialchars($message)); ?>
                    </div>
                </div>
                
                <div class="arrow">⬇️</div>
                
                <div class="message-section">
                    <h3>Messaggio Originale:</h3>
                    <div class="message-content original">
                        <?php echo nl2br(htmlspecialchars($decrypted)); ?>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['original_message']) && $_SESSION['original_message'] === $decrypted): ?>
                    <div class="success-badge">
                        ✓ Decifratura corretta! Il messaggio corrisponde all'originale.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-actions">
            <a href="index.php" class="btn btn-secondary">Torna alla Home</a>
        </div>
    </div>
    
    <script>
        function toggleKeyField() {
            const vigenereRadio = document.querySelector('input[value="vigenere"]');
            const keyGroup = document.getElementById('key-group');
            const keyInput = document.getElementById('key');
            
            if (vigenereRadio.checked) {
                keyGroup.style.display = 'block';
                keyInput.required = true;
            } else {
                keyGroup.style.display = 'none';
                keyInput.required = false;
            }
        }
        
        toggleKeyField();
    </script>
</body>
</html> 