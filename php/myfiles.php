<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../html/login.html");
    exit;
}

if ($_SESSION["role"] !== "admin") {
    exit("You don't have permission to upload files.");
}

require_once "db.php";
if (!$dbHandler) {
    exit("Database connection failed:.");
}


$stmt = $dbHandler->prepare("SELECT id, email FROM users WHERE role != 'admin'");
$stmt->execute();
$visitors = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch assoc will return results as an associativie array

$stmt = $dbHandler->prepare("SELECT id, name FROM years");
$stmt->execute();
$years = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = "";

//file uploader//
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["uploadedFile"])) {
    $maxSize = 3 * 1024 * 1024;

    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $yearId = filter_input(INPUT_POST, "year_id", FILTER_VALIDATE_INT);

    if (!$yearId) {
        $message = "Please select a year";
    } else {

        $allowedTypes = [
            "image/jpeg",
            "image/png",
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
        ];

        $fileError = $_FILES["uploadedFile"]["error"];
        $fileSize  = $_FILES["uploadedFile"]["size"];
        $fileTmp   = $_FILES["uploadedFile"]["tmp_name"];
        $fileName  = $_FILES["uploadedFile"]["name"];

        if ($fileError !== 0) {
            $message = "Upload error.";
        } elseif ($fileSize > $maxSize) {

            $message = "File too large (max 3MB).";
        } else {

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fileTmp);
            if (!in_array($mimeType, $allowedTypes)) {
                $message = "Invalid file type. Allowed: PDF, Word, Excel";
            } else {

                $newFileName = basename($fileName);

                if (move_uploaded_file($fileTmp, "upload/" . $fileName)) {

                    $stmt = $dbHandler->prepare(
                        "INSERT INTO files (filename, title, description, uploaded_by, year_id)
                         VALUES (:filename, :title, :description, :user, :year)"
                    );

                    $stmt->bindParam(":filename", $fileName);
                    $stmt->bindParam(":title", $title);
                    $stmt->bindParam(":description", $description);
                    $stmt->bindParam(":user", $_SESSION["user_id"]); //who uploaded file?
                    $stmt->bindParam(":year", $yearId);

                    $stmt->execute();
                    $fileId = $dbHandler->lastInsertId();

                    if (!empty($_POST["access"])) {
                        foreach ($_POST["access"] as $visitorId) {
                            $stmt = $dbHandler->prepare(
                                "INSERT INTO file_access (file_id, user_id)
                                 VALUES (:file, :user)"
                            );
                            $stmt->bindParam(":file", $fileId);
                            $stmt->bindParam(":user", $visitorId);
                            $stmt->execute();
                        }
                    }

                    $message = "File uploaded successfully.";
                } else {
                    $message = "Couldn't move uploaded file";
                }
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My files</title>
    <link rel="stylesheet" href="../css/myfiles.css">
</head>

<body>
    <header class="header">
        <a href="../php/myportfolio.php"><img src="../images/kzlogo.png" alt="kzLogo" class="kzLogo"></a>
        <ul class="links">
            <li>About me</li>
            <?php if (isset($_SESSION["user_id"])) {
            ?>
                <li class="loginButton">
                    <a href="../php/logout.php">Log out</a>
                </li>
            <?php
            } else {
            ?>
                <li class="loginButton">
                    <a href="../html/login.html">Log in</a>
                </li>
            <?php
            }
            ?>
        </ul>
    </header>

    <div class="fileuploadContainer">
        <div class="fileuploadBox">
            <h1>Upload your files</h1>
            <!--ENCTYPE-->
            <form action="myfiles.php" method="post" enctype="multipart/form-data" class="uploadForm">

                <input type="text" name="title" placeholder="File title" required>
                <textarea name="description" placeholder="Description"></textarea>

                <label for="file" class="choosefileLabel">Choose your file</label>
                <input type="file" name="uploadedFile" id="file" required>

                <div class="accessBox">
                    <h2>Who's viewing the file?</h2>
                    <?php foreach ($visitors as $visitor) {
                    ?>
                        <label>
                            <input type="checkbox" name="access[]" value="<?php echo $visitor['id']; ?>">
                            <?php echo $visitor['email']; ?>
                        </label>
                    <?php
                    }
                    ?>
                </div>

                <div class="moduleBox">
                    <h2>Select Year</h2>

                    <select name="year_id" required>
                        <option value="">Choose year</option>
                        <?php foreach ($years as $year) { ?>
                            <option value="<?php echo $year['id']; ?>">
                                <?php echo $year['name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button type="submit" class="uploadButton">Upload</button>
            </form>
            <?php echo $message; ?>
        </div>
</body>

</html>