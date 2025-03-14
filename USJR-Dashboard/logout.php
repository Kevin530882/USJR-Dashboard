<?php
session_save_path("C:\Users\cabal\OneDrive\Desktop\Kevin\session-save-path");
session_start();

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

$sessionFile = session_save_path() . "/sess_" . session_id();
if (file_exists($sessionFile)) {
    unlink($sessionFile);
}

header("Location: login.php");
exit();