<?php
include 'db_connect.php'; // Upewnij się, że ścieżka do pliku jest poprawna

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $carId = $_GET['id'];

    $sql = "SELECT model, price, capacity, fuel_type FROM cars WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $carId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $car = $result->fetch_assoc();
                echo json_encode(['success' => true, 'car' => $car]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nie znaleziono samochodu.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Błąd wykonania zapytania.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Błąd przygotowania zapytania.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Brak ID samochodu.']);
}

$conn->close();
?>
