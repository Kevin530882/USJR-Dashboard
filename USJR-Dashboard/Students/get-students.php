<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json");

$db = new PDO("mysql:host=localhost; dbname=usjr", "root", "root");

$stmt = $db->prepare("SELECT
                    s.studid, s.studlastname, s.studfirstname, s.studmidname,
                    s.studcollid, s.studprogid, c.collfullname, p.progfullname, s.studyear
                    FROM students s JOIN colleges c ON s.studcollid = c.collid
                    LEFT JOIN programs p ON s.studprogid = p.progid ORDER BY s.studid;");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
