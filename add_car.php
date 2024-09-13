<?php
session_start();
include 'db_connect.php';

if ($_SESSION['is_admin']) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $model = $_POST['model'];
        $price = $_POST['price'];
        $capacity = $_POST['capacity'];
        $fuel_type = $_POST['fuel'];

        // Sprawdzanie przesyłania plików
        if (isset($_FILES['image'])) {
            $image = $_FILES['image']['name'];
            $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($imageFileType, $allowedTypes)) {
                if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
                    // Przenoszenie przesłanego pliku bezpośrednio do głównego folderu projektu
                    $target_file = basename($image);

                    // Przenoszenie przesłanego pliku do głównego folderu
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        // Zapisz dane do bazy danych
                        $sql = "INSERT INTO cars (model, price, image, capacity, fuel_type) VALUES ('$model', '$price', '$image', '$capacity', '$fuel_type')";
                        if ($conn->query($sql) === TRUE) {
                            header("Location: /ProjektWWW/admin.html");
                        } else {
                            echo "Błąd SQL: " . $conn->error;
                        }
                    } else {
                        echo "Błąd przesyłania pliku.";
                    }
                } else {
                    echo "Błąd przesyłania pliku: " . $_FILES['image']['error'];
                }
            } else {
                echo "Niedozwolony format pliku. Obsługiwane formaty to: .jpg, .jpeg, .png, .gif.";
            }
        } else {
            echo "Brak pliku w przesyłce.";
        }
    }
} else {
    echo "Brak uprawnień.";
}
?>
