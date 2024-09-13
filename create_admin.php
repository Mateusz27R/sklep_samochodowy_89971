        <?php
        include 'db_connect.php';

        // Dane do konta administratora
        $username = 'admin';
        $password = 'admin123'; // Zmień na własne hasło

        // Hashowanie hasła
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Sprawdzenie, czy użytkownik już istnieje
        $sql_check = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($sql_check);

        if ($result->num_rows > 0) {
            echo "Użytkownik z nazwą '$username' już istnieje.";
        } else {
            // Dodanie konta administratora
            $sql = "INSERT INTO users (username, password, is_admin) VALUES ('$username', '$hashed_password', 1)";
            if ($conn->query($sql) === TRUE) {
                echo "Konto administratora utworzone pomyślnie.";
            } else {
                echo "Błąd: " . $conn->error;
            }
        }

        $conn->close();
        ?>
