<?php
    session_save_path("C:\Users\cabal\OneDrive\Desktop\Kevin\session-save-path");
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Login</title>
    <style>
        body {
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background: url('background.png') center no-repeat;
            background-size:cover;
        }
        table {
            border-spacing: 5px;
        }
        th {
            text-align: center;
            height: 50px;
            font-size: 2rem;
        }
        tfoot tr td {
            text-align:center;
        }
        form {
            border: 1px solid black;
            padding: 3px;
        }
    </style>
</head>
<body>
    
    <form action="login-process.php" method="post" class="bg-white">
        <table>
            <thead>
                <tr>
                    <th class="bg-black text-white" colspan="2">User Login</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Username: </td>
                    <td>
                        <input type="text" name="username" id="username">
                    </td>
                </tr>
                <tr>
                    <td>Password: </td>
                    <td>
                        <input type="password" name="password" id="password">
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <button class="btn btn-info" name="submit">Login</button>
                        <button class="btn btn-danger" type="reset">Clear</button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</body>
</html>
