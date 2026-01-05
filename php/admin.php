<?php

require_once("db.php");

if (!$dbHandler) {
    die("No database connection");
}

try {
    $email = "admin@localhost";
    $pass = "MyAdmin321";
    $role = "admin";

    $stmt = $dbHandler->prepare(
        "INSERT INTO users (email, password_hash, role)
        VALUES (:email, :hashedpass, :role)"

    );

    $hashedPass = password_hash($pass, PASSWORD_BCRYPT);

    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->bindParam(":hashedpass", $hashedPass, PDO::PARAM_STR);
    $stmt->bindParam(":role", $role, PDO::PARAM_STR);

    $stmt->execute();

    echo "Admin user created successfully";
} catch (Exception $ex) {
    printError($ex);
}
