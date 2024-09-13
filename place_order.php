<?php
include 'db_connect.php';

header('Content-Type: application/json');

if (isset($_POST['total_price'])) {
    $userId = 1; // Możesz użyć identyfikatora zalogowanego użytkownika, jeśli masz system logowania
    $totalPrice = $_POST['total_price'];
    
    $sql = "INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'oczekujące')";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("id", $userId, $totalPrice);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Błąd podczas składania zamówienia']);
        }
        $stmt->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych']);
}

$conn->close();
?>
