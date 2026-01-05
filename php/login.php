<?php
session_start();
$msg = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)) {
        $msg[] = "Invalid e-mail address.";
    }

    if (!$pass = filter_input(INPUT_POST, "password")) {
        $msg[] = "Please provide a password";
    }

    if (count($msg) == 0) {

        require_once("db.php");

        if ($dbHandler) {
            try {

                $stmt = $dbHandler->prepare(
                    "SELECT id, password_hash, role
                    FROM users
                    WHERE email = :email"
                );

                $stmt->bindParam(":email", $email,  PDO::PARAM_STR);
                $stmt->execute();
            } catch (Exception $ex) {
                printError($ex);
            }
        }

        if ($stmt && $stmt->rowCount() == 1) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (password_verify($pass, $results[0]["password_hash"])) {



                $_SESSION["user_id"] = $results[0]["id"];
                $_SESSION["role"] = $results[0]["role"];

                header("Location: ../html/myportfolio.html");
                exit;
            }
        } else {
            $msg[] = "Invalid e-mail or password.";
        }
    }
}
