<?php
$message = "";

if ($_SERVER["REQUEST_METHOD" == "POST"]) {
    $fileSize = (3 * 1024 * 1024);

    if ($_FILES["uploadedFile"]["error"] == 0) {
        if ($_FILES["uploadedFile"]["size"] < $fileSize) {
            $acceptedFileTypes = ["image/gif", "image/jpeg", "image/jpg"];
            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);

            $uploadedFileType = finfo_file($filenfo, $_FILES["uploadedFile"]["tmp_name"]);
            if (in_array($uploadedFileType, $acceptedFileTypes)) {
                if (!file_exists("upload/" . $_FILES["uploadedFile"]["tmp_name"])) {
                    if (move_uploaded_file("upload/" . $_FILES["uploadedFile"]["tmp_name"], "upload/" . $_FILES["uploadedFile"]["name"])) {
                        $message .= "Upload:";
                    }
                }
            }
        }
    }
}
