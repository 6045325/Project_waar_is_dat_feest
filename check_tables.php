<?php
$pdo = new PDO('mysql:host=localhost;dbname=Eventify;charset=utf8mb4', 'root', '');
$stmt = $pdo->query('SHOW TABLES');
echo "Tables in Eventify database:\n";
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo "- " . $row[0] . "\n";
}

echo "\n\nChecking deelnemers/participants table structure:\n";
try {
    $stmt = $pdo->query('DESCRIBE deelnemers');
    while ($row = $stmt->fetch()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "No deelnemers table found\n";
}

echo "\n\nChecking activiteit table structure:\n";
try {
    $stmt = $pdo->query('DESCRIBE activiteit');
    while ($row = $stmt->fetch()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "No activiteit table found\n";
}
?>
