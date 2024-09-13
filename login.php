<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['username'] ?? $_POST['email'];
    $password = $_POST['password'];

    $sql_admin = "SELECT * FROM users WHERE username=?";
    $stmt_admin = $conn->prepare($sql_admin);
    $stmt_admin->bind_param("s", $login);// przypisuje
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows > 0) {
        $row_admin = $result_admin->fetch_assoc();
        if (password_verify($password, $row_admin['password'])) {
            $_SESSION['user_id'] = $row_admin['id'];
            $_SESSION['is_admin'] = $row_admin['is_admin'];
            header("Location: admin.html");
            exit();
        } else {
            echo "Błędne hasło dla administratora.";
        }
    } else {
        $sql_user = "SELECT * FROM users1 WHERE email=?";
        $stmt_user = $conn->prepare($sql_user);                                                                        
        $stmt_user->bind_param("s", $login);//s-string
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows > 0) {
            $row_user = $result_user->fetch_assoc();
            if (password_verify($password, $row_user['password'])) {
                $_SESSION['user_id'] = $row_user['id'];
                header("Location: index.php");
                exit();
            } else {
                echo "Błędne hasło dla użytkownika.";
            }
        } else {
            echo "Nie znaleziono użytkownika ani administratora.";
        }
    }
}
?>
