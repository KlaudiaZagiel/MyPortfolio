
<?php
$dbHandler = null;
try {
    $dbHandler = new PDO("mysql:host=mysql;dbname=portfoliousers;charset=utf8", "root", "qwerty");
} catch (Exception $ex) {
    printError($ex);
}

function printError(String $err)
{
    echo "<h1>The following error occured</h1>
          <p>{$err}</p>";
    exit;
}
