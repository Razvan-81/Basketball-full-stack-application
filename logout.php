<?php
session_start();

// Salvează username-ul pentru mesajul de confirmare (opțional)
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Distruge toate datele sesiunii
session_unset();
session_destroy();

// Dacă se dorește să se distrugă complet sesiunea, șterge și cookie-ul de sesiune
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect la index.php cu mesaj de confirmare
header("Location: index.php?logout=success");
exit;
?>