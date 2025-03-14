<?php

$db = new PDO("mysql:host=localhost; dbname=usjr", "root", "root");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $action = isset($data['action']) ? $data['action'] : null;
    $id = isset($data['id']) ? $data['id'] : null;

    if (!$action || !$id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action or ID']);
        exit;
    }

    if ($action === 'edit') {
        $fname = $data['fname'];
        $sname = $data['sname'];
        $college = $data['college'];
        $department = $data['department'];

        $sql = "UPDATE programs SET
                    progfullname = ?,
                    progshortname = ?,
                    progcollid = ?,
                    progcolldeptid = ?
                WHERE progid = ?;";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $fname, PDO::PARAM_STR);
        $stmt->bindParam(2, $sname, PDO::PARAM_STR);
        $stmt->bindParam(3, $college, PDO::PARAM_INT);
        $stmt->bindParam(4, $department, PDO::PARAM_INT);
        $stmt->bindParam(5, $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Program updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update program']);
        }
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM programs WHERE progid = ?";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Program deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete program']);
        }
    }
}
