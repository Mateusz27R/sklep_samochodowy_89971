<?php
include 'db_connect.php'; // Połączenie z bazą danych

$model = $_GET['model'];

// Przygotowanie zapytania do bazy danych
$sql = "SELECT * FROM cars WHERE model = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $model);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Zwrócenie danych samochodu
    echo json_encode(['success' => true, 'car' => $row]);
} else {
    echo json_encode(['success' => false]);
}
?>
