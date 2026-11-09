<?php

include "./dataset.php";

// echo json_encode($products);

$product_names = array_column($products, 'name');

echo "<h2>array_column(\$products, 'name')</h2>";
foreach($product_names as $index => $product_name){
    $index++;
    echo "<hr> $index) $product_name <br>";
}


echo "<br>";
echo "<br>";
echo "<br>";
echo "<hr>";
echo "<br>";
echo "<br>";
echo "<br>";


$sum=0;
$avg=0;



foreach($products as $product){
    $sum += $product['price'];
}

$avg = $sum/count($products);
$cheap_products = array_filter($products, fn($product)=>$product['price']<$avg);
echo "<h2> The total Price of all products in the inventory is £$sum and the average price is £$avg so any product that cost less than £$avg is considered cheap! </h2>";

echo "<hr>";
echo "<h2>Cheap Products</h2>";
echo "<hr><br>";

foreach($cheap_products as $k=>$v){
        echo $v['name']." ------------------------------ £".$v['price'],"<br><hr><br>";
}

// foreach($cheap_products as $product){
//     echo $cheap_products['name']." --------------------- £".$cheap_products['price'];
// }
$arr1=[1,2,3,4,5];
$arr2=[6,7,8,9,10];
$merged_arr=[...$arr1, ...$arr2];
echo "<hr>";
echo "<h2>Merged array using ...\$array</h2>";
foreach($merged_arr as $arr){
    echo $arr."-";
}
echo "<hr><br>";
?>