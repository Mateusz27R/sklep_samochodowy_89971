<?php
include 'db_connect.php';

header('Content-Type: application/json');

if (isset($_POST['id'])) {
    $carId = $_POST['id'];

    $sql = "DELETE FROM cars WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $carId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Błąd podczas usuwania samochodu']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Błąd w przygotowaniu zapytania']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Brak ID samochodu']);
}

$conn->close();
?>
