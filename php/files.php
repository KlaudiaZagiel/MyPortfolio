<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../html/login.html");
    exit;
}

require_once "db.php";

// fallback connection (same idea as before)
if (!$dbHandler) {
    $dbHandler = new PDO(
        "mysql:host=localhost;dbname=portfoliousers;charset=utf8",
        "root",
        "qwerty"
    );
}

$userId = $_SESSION["user_id"];
$role = $_SESSION["role"];

if ($role === "admin") {

    $stmt = $dbHandler->query("SELECT * FROM files");
} else {

    $stmt = $dbHandler->prepare(
        "SELECT f.*
         FROM files f
         JOIN file_access fa ON f.id = fa.file_id
         WHERE fa.user_id = :user"
    );
    $stmt->execute([
        ":user" => $userId
    ]);
}

$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your files</title>
    <link rel="stylesheet" href="../css/files.css">
</head>

<body>

    <header class="header">
        <img src="../images/kzlogo.png" alt="kzLogo" class="kzLogo">
        <ul class="links">
            <li><a href="myportfolio.php">Home</a></li>
            <li>About me</li>
            <li class="loginButton">
                <a href="logout.php">Log out</a>
            </li>
        </ul>
    </header>

    <h1>Your files</h1>

    <div class="fileuploadContainer">
        <div class="fileuploadBox">
            <?php if (empty($files)): ?>
                <p>No files available.</p>
            <?php endif; ?>

            <?php foreach ($files as $file): ?>
                <div class="fileCard">
                    <h3><?= htmlspecialchars($file["title"]) ?></h3>
                    <p><?= htmlspecialchars($file["description"]) ?></p>
                    <a href="viewfile.php?id=<?= $file["id"] ?>">View file</a>
                </div>
        </div>
    </div>
<?php endforeach; ?>

</body>

</html>