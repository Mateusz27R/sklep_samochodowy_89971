<?php
session_start();
include 'db_connect.php';

if ($_SESSION['is_admin']) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $car_id = $_POST['car_id'];
        $model = $_POST['model'];
        $price = $_POST['price'];
        $capacity = $_POST['capacity'];
        $fuel_type = $_POST['fuel'];

        // Sprawdzanie przesyłania plików
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $image = $_FILES['image']['name'];
            $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($imageFileType, $allowedTypes)) {
                $target_file = basename($image);
                move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

                // Aktualizacja rekordu z nowym obrazem
                $sql = "UPDATE cars SET model='$model', price='$price', image='$target_file', capacity='$capacity', fuel_type='$fuel_type' WHERE id='$car_id'";
            } else {
                echo "Niedozwolony format pliku.";
                exit;
            }
        } else {
            // Aktualizacja rekordu bez zmiany obrazu
            $sql = "UPDATE cars SET model='$model', price='$price', capacity='$capacity', fuel_type='$fuel_type' WHERE id='$car_id'";
        }

        if ($conn->query($sql) === TRUE) {
            header("Location: admin.html");
        } else {
            echo "Błąd SQL: " . $conn->error;
        }
    }
} else {
    echo "Brak uprawnień.";
}
?>
