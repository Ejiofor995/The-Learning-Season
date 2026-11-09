<?php
if ($_SERVER['REQUEST_METHOD']=='POST'){
    $secret = $_POST['secret_info'] ?? '';
    echo "<br><hr>","<h2>Latest Gossip, straight from the oven:</h2><br> ".$secret."<br><hr>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post super global</title>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="secret_info">
        <button>Spill the bean</button>
    </form>
</body>
</html>