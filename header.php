<?php
session_start();
?>
<header>
    <nav>
        <a href="index.php">
            <img src="logo.png" alt="Logo BMW" class="logo">
        </a>
        <div class="nav-center">
            <a href="index.php"><b>Strona Główna</b></a>
            <a href="all_models.php"><b>Wszystkie Modele</b></a>
        </div>
        <div class="nav-right">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php"><b>Zaloguj się</b></a>
                <a href="register.php"><b>Zarejestruj się</b></a>
            <?php else: ?>
                <a href="cart.php" id="cart-link"><b>Koszyk</b></a>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="admin.php"><b>Panel Admina</b></a>
                <?php endif; ?>
                <a href="logout.php"><b>Wyloguj się</b></a>
            <?php endif; ?>
        </div>
    </nav>
</header>
