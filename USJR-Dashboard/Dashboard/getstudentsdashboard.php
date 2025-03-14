<?php
header('Content-Type: application/json');

try {
    $db = new PDO("mysql:host=localhost; dbname=usjr", "root", "root");

    $stmt = $db->prepare("
        SELECT c.collfullname, COUNT(s.studid) AS num_students
        FROM colleges c
        LEFT JOIN students s ON c.collid = s.studcollid
        GROUP BY c.collid
    ");
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
