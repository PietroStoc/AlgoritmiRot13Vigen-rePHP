<?php
session_start();
require_once 'Cipher.php';

// Verifica che la richiesta sia POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Validazione input
$message = $_POST['message'] ?? '';
$algorithm = $_POST['algorithm'] ?? '';
$key = $_POST['key'] ?? '';

// Salva i dati in sessione per ripopolare il form se necessario
$_SESSION['last_message'] = $message;
$_SESSION['last_algorithm'] = $algorithm;
$_SESSION['last_key'] = $key;

// Controlli di validazione
if (empty($message)) {
    $_SESSION['error'] = 'Il messaggio non può essere vuoto';
    header('Location: index.php');
    exit();
}

if (!in_array($algorithm, ['rot13', 'vigenere'])) {
    $_SESSION['error'] = 'Algoritmo non valido';
    header('Location: index.php');
    exit();
}

if ($algorithm === 'vigenere' && empty($key)) {
    $_SESSION['error'] = 'La chiave Vigenère è obbligatoria';
    header('Location: index.php');
    exit();
}

if ($algorithm === 'vigenere' && !ctype_alpha($key)) {
    $_SESSION['error'] = 'La chiave deve contenere solo lettere';
    header('Location: index.php');
    exit();
}

// Esegui la cifratura
try {
    $cipher = CipherFactory::create($algorithm, $message, $key);
    $encrypted = $cipher->encrypt();
    
    // Salva i risultati in sessione per la decifratura
    $_SESSION['encrypted_message'] = $encrypted;
    $_SESSION['original_message'] = $message;
    $_SESSION['cipher_algorithm'] = $algorithm;
    $_SESSION['cipher_key'] = $key;
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Errore durante la cifratura: ' . $e->getMessage();
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaggio Cifrato</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container">
        <h1>Messaggio Cifrato</h1>
        
        <div class="result-box success">
            <h2>Algoritmo utilizzato: <?php echo strtoupper($algorithm); ?></h2>
            <?php if ($algorithm === 'vigenere'): ?>
                <p class="key-info">Chiave: <strong><?php echo htmlspecialchars($key); ?></strong></p>
            <?php endif; ?>
            
            <div class="message-section">
                <h3>Messaggio Originale:</h3>
                <div class="message-content original">
                    <?php echo nl2br(htmlspecialchars($message)); ?>
                </div>
            </div>
            
            <div class="message-section">
                <h3>Messaggio Cifrato:</h3>
                <div class="message-content encrypted">
                    <?php echo nl2br(htmlspecialchars($encrypted)); ?>
        </div>
        
        <div class="statistics">
            <h3>Statistiche</h3>
            <ul>
                <li>Lunghezza messaggio originale: <strong><?php echo strlen($message); ?></strong> caratteri</li>
                <li>Lunghezza messaggio cifrato: <strong><?php echo strlen($encrypted); ?></strong> caratteri</li>
                <li>Lettere nel messaggio: <strong><?php echo preg_match_all('/[a-zA-Z]/', $message); ?></strong></li>
            </ul>
        </div>
        
        <div class="form-actions">
            <a href="index.php" class="btn btn-secondary">⬅Cifra Nuovo Messaggio</a>
            <a href="decifra.php" class="btn btn-primary">Vai a Decifratura</a>
        </div>
    </div>
</body>
</html>