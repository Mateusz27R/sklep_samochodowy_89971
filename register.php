<?php
include 'db_connect.php';

// Sprawdzanie, czy formularz został przesłany
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hashowanie hasła
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Sprawdzanie, czy użytkownik już istnieje
    $sql_check = "SELECT * FROM users1 WHERE email='$email'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        echo "Użytkownik z tym emailem już istnieje.";
    } else {
        // Dodanie nowego użytkownika
        $sql = "INSERT INTO users1 (first_name, last_name, phone, email, password) VALUES ('$first_name', '$last_name', '$phone', '$email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            echo "Rejestracja zakończona sukcesem.";
            header("Location: /ProjektWWW/index.html");
        } else {
            echo "Błąd: " . $conn->error;
        }
    }

    $conn->close();
}
?>
