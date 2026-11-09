<?php 
  // Check request type
  if ($_SERVER['REQUEST_METHOD']=='POST'){
    // sanitize user data to avoid js scripts and sql injection before storing the data
    $name = htmlspecialchars($_POST['name']);
  } else {
    //If there is no post request then initiate the name variable with no data
    $name;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form submission</title>
</head>
<body>

  <!-- Check if there is data in the name variable, if so then display a message with the name-->
  <?php if($name){ ?>
                
    <h2>Ohh, hello there <?php echo $name; ?> </h2>

  <!-- But if the name variable has no databin it then display a message and form to get their name -->
  <?php }else{ ?>
               
  <h2>Hello, i am an Ai model built by a Brilliant young man called Dark Zuckerberg, may i know your name? </h2>
  <form action="" method="post">
    <input type="text" name="name" />
    <input type="submit" name="submit" value="Reply" />
  </form>
               
  <?php } ?>

</body>
</html>