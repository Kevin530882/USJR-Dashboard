<?php

$db = new PDO("mysql:host=localhost; dbname=usjr", "root", "root");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $action = isset($data['action']) ? $data['action'] : null;
    $id = isset($data['id']) ? $data['id'] : null;

    if ($action === 'edit') {
        $lname = $data['lname'];
        $fname = $data['fname'];
        $mname = $data['mname'];
        $college = $data['college'];
        $program = $data['program'];
        $year = $data['year'];

        $sql = "UPDATE students SET
                    studlastname = ?,
                    studfirstname = ?,
                    studmidname = ?,
                    studcollid = ?,
                    studprogid = ?,
                    studyear = ?
                WHERE studid = ?;";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(1, $lname, PDO::PARAM_STR);
        $stmt->bindParam(2, $fname, PDO::PARAM_STR);
        $stmt->bindParam(3, $mname, PDO::PARAM_STR);
        $stmt->bindParam(4, $college, PDO::PARAM_INT);
        $stmt->bindParam(5, $program, PDO::PARAM_INT);
        $stmt->bindParam(6, $year, PDO::PARAM_INT);
        $stmt->bindParam(7, $id, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Student updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update student']);
        }
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM students WHERE studid = ?";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete student']);
        }
    }
}
?>
