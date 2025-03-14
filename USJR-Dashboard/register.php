<?php

$db = new PDO("mysql:host=localhost; dbname=usjr", "root", "root");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="register.php" method="post">
        <table>
            <tr>
                <td>Username: </td>
                <td><input type="text" name="username" id=""></td>
            </tr>
            <tr>
                <td>Password: </td>
                <td><input type="password" name="password" id=""></td>
            </tr>
            <tr><td><button name="submit">Register</button></td></tr>
        </table>
    </form>
</body>
</html>

<?php

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $pass = password_hash($password, PASSWORD_BCRYPT);

    $insert = "INSERT INTO appusers (username, pass) VALUES (?, ?);";
    $stmt = $db->prepare($insert);
    $stmt->bindParam(1, $username, PDO::PARAM_STR);
    $stmt->bindParam(2, $pass, PDO::PARAM_STR);
    if ($stmt->execute()) {
        echo "User created successfully";
    }
}
