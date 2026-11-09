<?php

if ($_SERVER['REQUEST_METHOD']=='POST'){

  header('Access-Control-Allow-Origin:*');
  header('Content-Type: application/json');
  
  $pair=$_POST['pair'];
  $equity=$_POST['equity'];
  $risk_method=$_POST['risk_method'];
  $risk=$_POST['risk'];
  $entry_price=$_POST['entry_price'];
  $exit_price=$_POST['exit_price'];
  $risk_amount;

  [$point,$digit,$contract_size] = match ($pair){
    'btc/usd'=>[0.01,2,1],
    default=>[0,0,0],
  };

  if ($risk_method=='percentage'){
    $risk=$equity*($risk/100);
  }

  if ($point && $digit && $contract_size){
    if ($entry_price==$exit_price){
        $response=[
      'response'=>"Your entry and exit prices can't be the same",
      'error'=>1,
    ];

    echo json_encode($response);
      exit;
    }
    
    $lot_size=$risk/(abs($entry_price-$exit_price)*$contract_size);

    $response=[
      'response'=>max(0.01, round($lot_size,2)),
      'risk_amount'=>$risk,
      'error'=>0,
    ];

    echo json_encode($response);
    exit;
  }else{
    $response=[
      'response'=>'Sorry, i don\'t recognize this currency pair '."$pair",
      'error'=>1,
    ];
    
    echo json_encode($response);
    exit;
  }
}else{
  echo "<h1>Welcome to to Dark Zuckerberg's Lot size calculator 😀</h1>";
  exit;
}