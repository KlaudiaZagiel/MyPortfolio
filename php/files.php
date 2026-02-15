<?php
session_start();

if (!isset($_SESSION["user_id"])) {       //authentication. wo login, user can't access the content
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

//logged in user//
$userId = $_SESSION["user_id"];
$role = $_SESSION["role"];

//Check if year was selected//
if (isset($_GET["year"])) {
    $selectedYear = (int)$_GET["year"];
} else {
    $selectedYear = null;
}

//Folder view//
$stmt = $dbHandler->query("SELECT * FROM years");
$years = $stmt->fetchAll(PDO::FETCH_ASSOC);

$files = [];

//Who can view this file//
if ($selectedYear !== null) {

    if ($role === "admin") { //show all files from selected year, i have  full access

        $sql = "SELECT 
        files.id, files.filename, files.title, files.description, files.uploaded_by, files.year_id,
        years.name AS year_name
        FROM files
        INNER JOIN years
        ON files.year_id = years.id
        WHERE files.year_id = $selectedYear
        ";

        $stmt = $dbHandler->query($sql);
    } else {
        $sql = "SELECT 
    files.id, files.filename, files.title, files.description, files.uploaded_by, files.year_id, 
    years.name AS year_name
    FROM  files
    INNER JOIN years
    ON files.year_id = years.id
    INNER JOIN file_access
    ON files.id = file_access.file_id
    WHERE file_access.user_id = $userId
    AND files.year_id = $selectedYear
    ";
        $stmt = $dbHandler->query($sql);
    }
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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

    <h1>Your Files</h1>

    <div class="fileuploadContainer">
        <div class="fileuploadBox">

            <!-- Years-->
            <h2>Select Year</h2>

            <div class="yearFolders">
                <?php foreach ($years as $year) { ?>
                    <a href="files.php?year=<?php echo $year['id']; ?>">
                        <div class="yearCard">
                            <?php echo htmlspecialchars($year['name']); ?>
                        </div>
                    </a>
                <?php } ?>
            </div>

            <!-- Files-->
            <?php
            foreach ($files as $file) {
            ?>
                <div class="fileCard">
                    <h3><?= htmlspecialchars($file["title"]) ?></h3>
                    <p><?= htmlspecialchars($file["description"]) ?></p>
                    <a href="viewfile.php?id=<?= $file["id"] ?>">View file</a>
                </div>
            <?php
            }
            ?>
        </div>
    </div>


</body>

</html>