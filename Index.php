<?php
session_start();

// Pulisci messaggi di errore precedenti
if (!isset($_POST['submit'])) {
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cifratura Messaggi - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container">
        <h1>Cifratura Messaggi</h1>
        <p class="subtitle">Inserisci il messaggio da cifrare e scegli l'algoritmo</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error">
                <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="cifrato.php" method="POST" class="cipher-form">
            <div class="form-group">
                <label for="message">Messaggio da cifrare:</label>
                <textarea 
                    id="message" 
                    name="message" 
                    rows="1" 
                    required
                    placeholder="Inserisci..."
                ><?php echo isset($_SESSION['last_message']) ? htmlspecialchars($_SESSION['last_message']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Algoritmo di cifratura:</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input 
                            type="radio" 
                            name="algorithm" 
                            value="rot13" 
                            <?php echo (!isset($_SESSION['last_algorithm']) || $_SESSION['last_algorithm'] == 'rot13') ? 'checked' : ''; ?>
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
                            <?php echo (isset($_SESSION['last_algorithm']) && $_SESSION['last_algorithm'] == 'vigenere') ? 'checked' : ''; ?>
                            onchange="toggleKeyField()"
                        >
                        <div>
                            <strong>Vigenère</strong>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="form-group" id="key-group" style="display: <?php echo (isset($_SESSION['last_algorithm']) && $_SESSION['last_algorithm'] == 'vigenere') ? 'block' : 'none'; ?>;">
                <label for="key">Chiave Vigenère:</label>
                <input 
                    type="text" 
                    id="key" 
                    name="key" 
                    placeholder="Inserisci la chiave (solo lettere)"
                    pattern="[A-Za-z]+"
                    value="<?php echo isset($_SESSION['last_key']) ? htmlspecialchars($_SESSION['last_key']) : ''; ?>"
                >
                <small>La chiave deve contenere solo lettere</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="submit" class="btn btn-primary">Cifra Messaggio</button>
                <button type="reset" class="btn btn-secondary">Cancella</button>
            </div>
        </form>
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