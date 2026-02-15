<?php
session_start();
$msg = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)) {
        $msg[] = "Invalid e-mail address.";
    }

    if (!$pass = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
        $msg[] = "Please provide a password";
    }

    if (empty($msg)) {

        require_once("db.php");

        if (!$dbHandler) {
            exit("Database connection failed.");
        }

        $stmt = $dbHandler->prepare(
            "SELECT
            id, password_hash, role
            FROM users
            WHERE email = :email"
        );

        $stmt->execute([
            ":email" => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pass, $user["password_hash"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role"] = $user["role"];

            header("Location: ../php/myportfolio.php");
            exit;
        } else {
            $msg[] = "Invalid e-mail or password.";
        }
    }
}
