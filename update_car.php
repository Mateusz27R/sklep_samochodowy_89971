<?php
include 'db_connect.php'; // Poprawna nazwa pliku

header('Content-Type: application/json');

if (isset($_POST['id'], $_POST['model'], $_POST['price'], $_POST['capacity'], $_POST['fuel_type'])) {
    $carId = $_POST['id'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $fuel_type = $_POST['fuel_type'];

    $sql = "UPDATE cars SET model = ?, price = ?, capacity = ?, fuel_type = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssi", $model, $price, $capacity, $fuel_type, $carId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Błąd podczas aktualizacji']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Błąd podczas przygotowywania zapytania']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych']);
}

$conn->close();
?>
