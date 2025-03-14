<?php
if (isset($_POST['cancel'])) {
    header("Location: view-departments.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Document</title>
    <style>
        input, select {
            width:300px;
            text-align:center;
        }
        body {
            display:flex;
            height:100vh;
            justify-content:center;
            align-items:center;
            background: url('../background.png') center no-repeat;
            background-size:cover;
        }
    </style>
</head>
<body>
    <form action="add-department.php" method="post" class="border border-3 border-dark rounded p-3 bg-white">
        <table>
            <tr>
                <td colspan="2" style="text-align:center">
                    <h3>Add a Department</h3>
                </td>
            </tr>
            <tr>
                <td>Department ID</td>
                <td><input type="number" name="deptid" id="deptid"></td>
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
                <td>College</td>
                <td>
                    <select name="colleges" id="colleges">
                        <option value="default">---- Select College ----</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center">
                    <button name="submit" class="btn btn-info text-white">Save</button>
                    <button type="reset" class="btn btn-danger" onclick="clear()">Clear</button>
                    <button name="cancel" class="btn btn-success" onclick="cancel()">Cancel</button>
                </td>
            </tr>
        </table>
    </form>
    <script src="../axios.min.js"></script>
    <script src="../axios.min.js.map"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        axios.get('../get-colleges-programs.php')
        .then(response => {
            console.log(response.data);
            const colleges = response.data.colleges;
            const collegeSelect = document.getElementById('colleges');

            colleges.forEach(college => {
                const option = document.createElement('option');
                option.value = college.collid;
                option.textContent = college.collfullname;
                collegeSelect.appendChild(option);
            });
        })
    </script>
</body>
</html>

<?php

if (isset($_POST['submit'])) {
    $colid = $_POST['colleges'];
    $fname = $_POST['fname'];
    $sname = $_POST['sname'];
    $deptid = $_POST['deptid'];

    if (empty($colid) || empty($fname) || empty($sname) || empty($deptid)) {
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
    
        $checkSql = "SELECT COUNT(*) FROM departments WHERE deptid = ?";
        $checkStmt = $db->prepare($checkSql);
        $checkStmt->bindParam(1, $deptid, PDO::PARAM_INT);
        $checkStmt->execute();
        $exists = $checkStmt->fetchColumn();

        if ($exists > 0) {
            echo '<script>
                Swal.fire({
                    icon: "warning",
                    title: "Duplicate ID",
                    text: "A department with this ID already exists. Please use a different ID.",
                }).then(() => {
                    window.history.back();
                });
            </script>';
            exit;
        }

        $sql = "INSERT INTO departments VALUES (?, ?, ?, ?);";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $deptid, PDO::PARAM_INT);
        $stmt->bindParam(2, $fname, PDO::PARAM_STR);
        $stmt->bindParam(3, $sname, PDO::PARAM_STR);
        $stmt->bindParam(4, $colid, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Department Added",
                    text: "The department has been added successfully!",
                }).then(() => {
                    window.location.href = "view-departments.php";
                });
            </script>';
        } else {
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to add the department. Please try again.",
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
