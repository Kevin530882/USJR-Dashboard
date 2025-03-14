<?php

$db = new PDO("mysql:host=localhost; dbname=usjr", "root", "root");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $action = isset($data['action']) ? $data['action'] : null;
    $id = isset($data['id']) ? $data['id'] : null;

    if (!$action || !$id) {
        echo json_encode    (['status' => 'error', 'message' => 'Invalid action or ID']);
        exit;
    }

    if ($action === 'edit') {
        $fname = $data['fname'];
        $sname = $data['sname'];

        $sql = "UPDATE colleges SET
                    collfullname = ?,
                    collshortname = ?
                WHERE collid = ?;";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $fname, PDO::PARAM_STR);
        $stmt->bindParam(2, $sname, PDO::PARAM_STR);
        $stmt->bindParam(3, $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Student updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update student']);
        }
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM colleges WHERE collid = ?";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete student']);
        }
    }
}
