<?php
// Start session and set csrf token if missing
session_start();

require_once "./Shipfast.php";

$shipfast = new Shipfast();

if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));

// Redirect to hompage if the Session email is already set, meaning they are already authenticated
if(isset($_SESSION['email'])) header('Location: ./index.php');

// instantiate errors and for data array variables
$errors = $formData=[];

// Helper function to escape message to html frontend
function display($output){
    return htmlspecialchars($output);
}

// Check if a POST request was sent
if ($_SERVER['REQUEST_METHOD']==='POST'){
    // Importing my upload tool for file upload which returns the url or error message
    require_once "./tools/upload.php";

    // verify hidden csrf token against session csrf token
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) $errors['csfr']="Invalid csrf token, please try again!";

    // Collect and trim all form data
    $formData=[
        'username'=>trim($_POST['username']),
        'email'=>trim($_POST['email']),
        'pass'=>(trim($_POST['pass'])),
        'pass_confirm'=>trim($_POST['pass_confirm']),
    ];


    // Validating form data, set an error if validation is not true
    if (mb_strlen($formData['username'])>20 || mb_strlen($formData['username'])<5) $errors['username']="username must be within 5 and 20 characters!";

    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors['email']="invalid email!";

    // if ($formData['pass']!==$formData['pass_confirm']) $errors['pass']='both passwords don\'t match!';

    // if ($formData['pass']==='' || $formData['pass_confirm']==='') $errors['pass']='Password can\'t be empty!';
    $pass = $shipfast->handlePassword($_POST['pass'],$_POST['pass_confirm' ]);
        
    
    $formData['pass']=$pass['ok'] ? $pass['response'] : null;
    if (!$formData['pass']) $errors['pass']=$pass['response'];
    
    // Now make use of the data if all validations were passed with no errors
    if (!$errors){
        $avater = uploadFile($_FILES['avater'], ['jpg','jpeg','png'], 10);
        if (!$avater['ok']) $errors['avater']=$avater['msg'];
            // 'avater'=> ($avater['ok'] ? $avater['path'] : $_POST['avater_url']) ?? $_COOKIE['avater'],
        
        if(!$errors){
            
            $formData['avater']=$avater['path'];
            foreach($formData as $k=>$v){
                // if($k==='pass' || $k==='pass_confirm'){
                //     continue;
                // }

                $_SESSION[$k]=$v;
            }


        header('Location: ./index.php'); exit;
        }
    }



}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

</head>
<body>

    <?php require_once "./tools/navbar.php" ?>

    <form action="" enctype="multipart/form-data" method="post">
        <h2>Registration Form</h2>

        <input type="hidden" name="csrf" value="<?= display($_SESSION['csrf'] ?? '') ?>">
        <hr>
        <br>

        

        <img id="preview" src="<?= display($formData['avater']??'') ?>" style="display:hidden">
        <br><br>
        <input type="hidden" name="avater_url" id="avater_url">
        <input type="file" accept="image/*" name="avater" placeholder="Upload Your avater" value="<?= display($_COOKIE['avater']??'') ?>" onchange="
        r=new FileReader(); 
        r.onload=e=>{preview.src=e.target.result; avater_url.value=e.target.result;}
        r.readAsDataURL(this.files[0])
        preview.style='display:block';
        " required>
        <br><br>
        <span style="color:red"><?= display($errors['avater']??'') ?></span>
        <br><br>

        <label for="username">Username</label>
        <br>
        <input type="text" name="username" value="<?= display($formData['username']??'') ?>">
        <br>
        <span class="text-lg text-error" style="color:red"><?= display($errors['username']??'') ?></span>

        <br><br>

        <label for="username">email</label>
        <br>
        <input type="text" name="email" value="<?= display($formData['email']??'') ?>">
        <br>
        <span class="text-lg text-error" style="color:red"><?= display($errors['email'] ?? '') ?></span>

        <br><br>

        
        <label for="username">password</label>
        <br>
        <input type="password" name="pass">
        <br>
        <span class="text-lg text-error" style="color:red"><?= display($errors['pass'] ?? '') ?></span>

        <br><br>
        
        <label for="username">confirm password</label>
        <br>
        <input type="password" name="pass_confirm">
        <br>
        <span class="text-lg text-error" style="color:red"><?= display($errors['pass']??'') ?></span>

        <br><br>

        <button>Register</button>


    </form>
</body>
</html>