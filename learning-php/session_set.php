<?php

session_start();

?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Session echo</title>
</head>
<body>

    <?php
        $_SESSION['username'] = 'Ejiofor';
        echo "


            <a href='http://localhost/learning-php/session_echo.php' target='_blank'>
                Go to echo page!
            </a>
        ";
    ?>

</body>
</html>