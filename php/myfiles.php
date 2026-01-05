<?php
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
        <a href="../html/myportfolio.html"><img src="../images/kzlogo.png" alt="kzLogo" class="kzLogo"></a>
        <ul class="links">
            <li>About me</li>
            <li class="loginButton">
                <a href="../php/login.php">Log in</a>
            </li>
        </ul>
    </header>

    <div class="fileuploadContainer">
        <div class="fileuploadBox">
            <h1>Upload your files</h1>
            <!--ENCTYPE-->
            <form action="myfiles.php" method="post" enctype="multipart/form-data" class="uploadForm">
                <label for="file" class="choosefileLabel">Choose your file</label>
                <input type="file" name="uploadedFile" id="file" />
                <button type="submit" class="uploadButton">Upload</button>
            </form>
            <?php echo $message; ?>
        </div>
</body>

</html>