<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">
            <span class="logo"></span>
            <span class="brand-text">CipherApp</span>
        </div>
        <ul class="nav-menu">
            <li>
                <a href="index.php" class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">
                    Home
                </a>
            </li>
            <li>
                <a href="cifrato.php" class="nav-link <?php echo ($current_page === 'cifrato.php') ? 'active' : ''; ?>">
                    Cifrato
                </a>
            </li>
            <li>
                <a href="decifra.php" class="nav-link <?php echo ($current_page === 'decifra.php') ? 'active' : ''; ?>">
                    Decifra
                </a>
            </li>
        </ul>
    </div>
</nav>