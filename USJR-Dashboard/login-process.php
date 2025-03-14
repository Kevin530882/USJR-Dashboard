<?php

session_save_path("C:\Users\cabal\OneDrive\Desktop\Kevin\session-save-path");
session_start();

$db = new PDO("mysql:host=localhost; dbname=usjr", "root", "root");



if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $_SESSION['user'] = $username;

    $sql = "SELECT username, pass FROM appusers WHERE username = ?;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $username, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        header("Location: Dashboard/dashboard.php");
        exit();
    } else {
        header("Location:".$_SERVER["HTTP_REFERER"]."?errcode=101&un=".$username);
        exit();
    }
}
