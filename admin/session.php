<?php
if (session_status() === PHP_SESSION_NONE) {
    // Start the session if it hasn't been started yet
    session_start();
}

function create_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Helper functions for managing login-states
function is_logged_in(): bool
{
    return isset($_SESSION["user_id"]);
}

// function require_login(): void
// {
//     if (!is_logged_in()) {
//         header("Location: index.php");
//         exit();
//     }
// }




?>