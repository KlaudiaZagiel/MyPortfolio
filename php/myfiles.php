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
    try {
        $dbHandler = new PDO(
            "mysql:host=localhost;dbname=portfoliousers;charset=utf8",
            "root",
            "qwerty"
        );
        $dbHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["uploadedFile"])) {
    $fileSize = (3 * 1024 * 1024); //3Mb

    if ($_FILES["uploadedFile"]["error"] == 0) {

        if ($_FILES["uploadedFile"]["size"] < $fileSize) {   //uploadedFile comes from my html input name ''uploadedFile''
            $acceptedFileTypes = ["image/gif", "image/jpg", "image/jpeg"];

            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
            $uploadedFileType = finfo_file($fileinfo, $_FILES["uploadedFile"]["tmp_name"]);

            if (in_array($uploadedFileType, $acceptedFileTypes)) {
                if (!file_exists("upload/" . $_FILES["uploadedFile"]["name"])) { //prevent overwiting existing file. if there is already file like that, stop upload

                    if (move_uploaded_file($_FILES["uploadedFile"]["tmp_name"], "upload/" . $_FILES["uploadedFile"]["name"])) { //move uploaded file to "upload/" folder

                        $stmt = $dbHandler->prepare(
                            "INSERT INTO files (filename, title, description, uploaded_by)
                    VALUES (:filename, :title, :description, :user)"
                        );

                        $stmt->execute([
                            ":filename" => $_FILES["uploadedFile"]["name"],
                            ":title" => $_POST["title"],
                            ":description" => $_POST["description"],
                            ":user" => $_SESSION["user_id"]
                        ]);

                        $fileId = $dbHandler->lastInsertId();

                        if (!empty($_POST["access"])) {
                            foreach ($_POST["access"] as $visitorId) {
                                $stmt = $dbHandler->prepare(
                                    "INSERT INTO file_access (file_id, user_id)
                                    VALUES (:file, :user)"
                                );
                                $stmt->execute([
                                    ":file" => $fileId,
                                    ":user" => $visitorId
                                ]);
                            }
                        }

                        $message = "<div class='upload-details'>";
                        $message .= "<p><strong>Upload: </strong> " . $_FILES["uploadedFile"]["name"] . "<br />";
                        $message .= "<p><strong>Type: </strong> " . $uploadedFileType . "<br />";
                        $message .= "<p><strong> Size: </strong> " . ($_FILES["uploadedFile"]["size"] / 1024) . "Kb<br />";
                        $message .= "<p><strong> Stored temporarily in: </strong> " . $_FILES["uploadedFile"]["tmp_name"] . "<br />";
                        $message .= "<p><strong> Stored permanently in: </strong> " . "upload/" . $_FILES["uploadedFile"]["name"];
                        $message .= "<p><strong>Uploaded file:</strong> " . htmlspecialchars($_FILES["uploadedFile"]["name"]) . "</p>";
                        $message .= "</div>";
                    } else {
                        $message .= "Something went wrong while uploading.";
                    }
                } else {
                    $message .= $_FILES["uploadedFile"]["name"] . " already exsists. ";
                }
            } else {
                $message .= "Invalid file type. File type must be: gif, jpg or jpeg.";
            }
        } else {
            $message .= "Invalid file size. File size must be less than " . $fileSize / 1024 / 1024 . "Mb.";
        }
    } else {
        $message .= "Error: " . $_FILES["uploadedFile"]["error"] . "<br />";
        $message .= "See <a href='https://www.php.net/manual/en/features.file-upload.errors.php' target='_BLANK'>PHP.net</a> for the explanation of the error messages.";
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
            <?php if (isset($_SESSION["user_id"])): ?>
                <li class="loginButton">
                    <a href="../php/logout.php">Log out</a>
                </li>
            <?php else: ?>
                <li class="loginButton">
                    <a href="../html/login.html">Log in</a>
                </li>
            <?php endif; ?>
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

                    <label class="accessOption">
                        <input type="checkbox" name="access[]" value="2">
                        Teacher
                    </label>

                    <label>
                        <input type="checkbox" name="access[]" value="3">
                        Supervisor
                    </label>
                </div>

                <button type="submit" class="uploadButton">Upload</button>
            </form>
            <?php echo $message; ?>
        </div>
</body>

</html>