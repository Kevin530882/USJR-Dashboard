<?php

try {
    $db = new PDO('mysql:host=localhost; dbname=usjr', 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $collegesQuery = "SELECT * FROM colleges";
    $collegesStmt = $db->query($collegesQuery);
    $colleges = $collegesStmt->fetchAll(PDO::FETCH_ASSOC);

    $programsQuery = "SELECT * FROM programs";
    $programsStmt = $db->query($programsQuery);
    $programs = $programsStmt->fetchAll(PDO::FETCH_ASSOC);

    $departmentsQuery = "SELECT * FROM departments";
    $departmentsStmt = $db->query($departmentsQuery);
    $departments = $departmentsStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'colleges' => $colleges,
        'programs' => $programs,
        'departments' => $departments
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}
