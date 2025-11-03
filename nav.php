<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <div class="nav-container">
        <ul class="nav-menu">
            <li>
                <a href="index.php" class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">
                    Home
                </a>
            </li>
            <li>
                <a href="cifrato.php" class="<?php echo ($current_page === 'cifrato.php') ? 'active' : ''; ?>">
                    Cifrato
                </a>
            </li>
            <li>
                <a href="decifra.php" class="<?php echo ($current_page === 'decifra.php') ? 'active' : ''; ?>">
                    Decifra
                </a>
            </li>
        </ul>
    </div>
</nav>