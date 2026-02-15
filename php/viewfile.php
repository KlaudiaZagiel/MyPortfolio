<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../html/login.html");
    exit;
}

if (!isset($_GET["id"])) {
    exit("No file selected.");
}

require_once "db.php";

if (!$dbHandler) {
    $dbHandler = new PDO(
        "mysql:host=localhost;dbname=portfoliousers;charset=utf8",
        "root",
        "qwerty"
    );
}

$fileId = (int)$_GET["id"];
$userId = $_SESSION["user_id"];
$role   = $_SESSION["role"];


//view file - admin sees everything, teacher depending on an access//
if ($role === "admin") {
    $stmt = $dbHandler->prepare(
        "SELECT * FROM files WHERE id = :id"
    );
    $stmt->execute([":id" => $fileId]);
} else {

    $stmt = $dbHandler->prepare(
        "SELECT files.*
         FROM files 
         INNER JOIN file_access
         ON files.id = file_access.file_id
         WHERE files.id = :file
         AND file_access.user_id = :user"
    );
    $stmt->execute([
        ":file" => $fileId,
        ":user" => $userId
    ]);
}

$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    exit("Access denied or file not found.");
}

$filePath = "upload/" . $file["filename"];

if (!file_exists($filePath)) {
    exit("File not found on server.");
}

//send this file to the browser so user can see it//
header("Content-Type: " . mime_content_type($filePath));
readfile($filePath);
exit;
