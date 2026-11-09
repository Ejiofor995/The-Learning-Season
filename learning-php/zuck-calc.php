<?php

header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD']=='POST'){
  $a=$_POST['a'];
  $o=$_POST['o'];
  $b=$_POST['b'];
}

if ($a && $o && $b){
  function calculate($a, $o, $b){
    $result = match ($o) {
      '*' => $a*$b,
      '/' => $a/$b,
      '+' => $a+$b,
      '-' => $a-$b,
      '^' => $a**$b,
      '%' => $a%$b,
      
      default => 'Something went wrong!, probably a wrong input 😟'
    };

    header('Content-Type: application/json');

    $result=[
      'result'=>$result,
    ];

    return json_encode($result);
  }
}


if ($a && $o && $b){
  echo calculate($a, $o, $b);
}else{
  echo "<h1>Welcome  🙋‍♂️, and thanks for using Zuck-calc API, a simple arithmetic calculator 🧮 designed and created by Dark Zuckerberg.</h1>";
}

?>

