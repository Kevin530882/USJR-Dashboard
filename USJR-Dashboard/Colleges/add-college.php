<?php
if (isset($_POST['cancel'])) {
    header("Location: view-colleges.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Add College</title>
    <style>
        input {
            width: 300px;
            text-align: center;
        }
        body {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            background: url('../background.png') center no-repeat;
            background-size: cover;
        }
    </style>
</head>
<body>
    <form action="add-college.php" method="post" class="border border-3 border-dark rounded p-3 bg-white">
        <table>
            <tr>
                <td colspan="2" style="text-align: center">
                    <h3>Add a College</h3>
                </td>
            </tr>
            <tr>
                <td>College ID</td>
                <td><input type="number" name="colid" id="colid"></td>
            </tr>
            <tr>
                <td>Full Name</td>
                <td><input type="text" name="fname" id="fname"></td>
            </tr>
            <tr>
                <td>Short Name</td>
                <td><input type="text" name="sname" id="sname"></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center">
                    <button name="submit" class="btn btn-info text-white">Save</button>
                    <button type="reset" class="btn btn-danger">Clear</button>
                    <button name="cancel" type="submit" class="btn btn-success">Cancel</button>
                </td>
            </tr>
        </table>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

<?php

if (isset($_POST['submit'])) {
    $colid = $_POST['colid'];
    $fname = $_POST['fname'];
    $sname = $_POST['sname'];

    if (empty($colid) || empty($fname) || empty($sname)) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Missing Input",
                text: "Please fill in all required fields!",
            }).then(() => {
                window.history.back();
            });
        </script>';
        exit;
    }

    try {
        $db = new PDO("mysql:host=localhost; dbname=usjr", "root", "root");
        
        $checkSql = "SELECT COUNT(*) FROM colleges WHERE collid = ?";
        $checkStmt = $db->prepare($checkSql);
        $checkStmt->bindParam(1, $colid, PDO::PARAM_INT);
        $checkStmt->execute();
        $exists = $checkStmt->fetchColumn();

        if ($exists > 0) {
            echo '<script>
                Swal.fire({
                    icon: "warning",
                    title: "Duplicate ID",
                    text: "A college with this ID already exists. Please use a different ID.",
                }).then(() => {
                    window.history.back();
                });
            </script>';
            exit;
        }

        $sql = "INSERT INTO colleges VALUES (?, ?, ?);";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $colid, PDO::PARAM_INT);
        $stmt->bindParam(2, $fname, PDO::PARAM_STR);
        $stmt->bindParam(3, $sname, PDO::PARAM_STR);
        $result = $stmt->execute();

        if ($result) {
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "College Added",
                    text: "The college has been added successfully!",
                }).then(() => {
                    window.location.href = "view-colleges.php";
                });
            </script>';
        } else {
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to add the college. Please try again.",
                }).then(() => {
                    window.history.back();
                });
            </script>';
        }
    } catch (PDOException $e) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Database Error",
                text: "An error occurred while connecting to the database.",
            }).then(() => {
                window.history.back();
            });
        </script>';
        error_log($e->getMessage());
    }
    exit;
}
