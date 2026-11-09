<?php 
try{
    require_once "./tools/db.php";
    $stmt=$pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$_GET['id']]);

    header('Location: ./status.php?success=1&service=delete');
}catch(PDOException $err){
    header('Location: ./status.php?success=0&service=delete');
    // exit('Request was unsuccessful: '.$err->getMessage());
}
?>