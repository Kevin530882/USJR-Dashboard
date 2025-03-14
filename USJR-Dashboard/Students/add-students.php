<?php
if (isset($_POST['cancel'])) {
    header("Location: student-listing.php");
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
    <form action="add-students.php" method="post" class="border border-3 border-dark rounded p-3 bg-white">
        <table>
            <tr><td colspan="2" style="text-align:center"><h3>Student Information Data Entry</h3></td></tr>
            <tr>
                <td>Student ID</td>
                <td><input type="number" name="studid" id=""></td>
            </tr>
            <tr>
                <td>First Name</td>
                <td><input type="text" name="studfirstname" id=""></td>
            </tr>
            <tr>
                <td>Middle Name</td>
                <td><input type="text" name="studmidname" id=""></td>
            </tr>
            <tr>
                <td>Last Name</td>
                <td><input type="text" name="studlastname" id=""></td>
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
                <td>Program</td>
                <td>
                    <select name="programs" id="programs">
                        <option value="default">---- Select Program ----</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Year</td>
                <td><input type="number" name="studyear" id=""></td>
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
            const programs = response.data.programs;
            const collegeSelect = document.getElementById('colleges');
            const programSelect = document.getElementById('programs');

            colleges.forEach(college => {
                const option = document.createElement('option');
                option.value = college.collid;
                option.textContent = college.collfullname;
                collegeSelect.appendChild(option);
            });

            collegeSelect.addEventListener('change', () => {
                programSelect.innerHTML = '';

                if (collegeSelect.value !== 'default') {
                    programs.forEach(program => {
                        if (program.progcollid == collegeSelect.value) {
                            const option = document.createElement('option');
                            option.value = program.progid;
                            option.textContent = program.progfullname;
                            programSelect.appendChild(option);
                        }
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = '---- Select Program ----';
                    programSelect.appendChild(option);
                }
            });
        })
        function clear () {
            const programSelect = document.getElementById('programs');
            programSelect.innerHTML = '';
            const option = document.createElement('option');
            option.value = '';
            option.textContent = '---- Select Program ----';
            programSelect.appendChild(option);
        }
    </script>
</body>
</html>

<?php

if (isset($_POST['submit'])) {
    $id = $_POST['studid'];
    $firstname = $_POST['studfirstname'];
    $middlename = $_POST['studmidname'];
    $lastname = $_POST['studlastname'];
    $college = $_POST['colleges'];
    $program = $_POST['programs'];
    $year = $_POST['studyear'];

    if (empty($id) || empty($firstname) || empty($lastname) || empty($college) || empty($program) || empty($year)) {
        echo "<script>Swal.fire({
            icon: 'error',
            title: 'Missing Input',
            text: 'Please fill in all required fields!',
        });</script>";
    } else {
        try {
            $db = new PDO("mysql:host=localhost; dbname=usjr", "root", "root");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $checkSql = "SELECT COUNT(*) FROM students WHERE studid = ?";
            $checkStmt = $db->prepare($checkSql);
            $checkStmt->bindParam(1, $id, PDO::PARAM_INT);
            $checkStmt->execute();
            $exists = $checkStmt->fetchColumn();

            if ($exists > 0) {
                echo "<script>Swal.fire({
                    icon: 'error',
                    title: 'Duplicate ID',
                    text: 'A student with this ID already exists!',
                }).then(() => {
                    window.history.back();
                });</script>";
                exit;
            } else {
                $sql = "INSERT INTO students (studid, studfirstname, studlastname, studmidname, studprogid, studcollid, studyear) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $firstname, PDO::PARAM_STR);
                $stmt->bindParam(3, $lastname, PDO::PARAM_STR);
                $stmt->bindParam(4, $middlename, PDO::PARAM_STR);
                $stmt->bindParam(5, $program, PDO::PARAM_INT);
                $stmt->bindParam(6, $college, PDO::PARAM_INT);
                $stmt->bindParam(7, $year, PDO::PARAM_INT);
                $result = $stmt->execute();

                if ($result) {
                    echo "<script>Swal.fire({
                        icon: 'success',
                        title: 'Student Added',
                        text: 'The student has been successfully added.',
                    }).then(function() {
                        window.location.href = 'student-listing.php';
                    });</script>";
                } else {
                    echo "<script>Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Student could not be added.',
                    });</script>";
                }
            }
        } catch (Exception $e) {
            echo "<script>Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred: " . $e->getMessage() . "',
            });</script>";
        }
    }
}
