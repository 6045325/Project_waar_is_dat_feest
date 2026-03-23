<?php
include 'db.php';

$stmt = $conn->prepare("SELECT * FROM activiteiten");
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>