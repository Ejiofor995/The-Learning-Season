<?php 
$indexed_array = [];
$indexed_array[]='Ippo';
array_push($indexed_array,'Mayami','Makanouchi');

$count=0;
$loop_header = 1;
foreach($indexed_array as $index){
    if($loop_header){
        echo "<h2>Looping Through Array</h2>";
        $loop_header=0;
    }
    $count++;
    echo "$count) $index <br>";
}
echo "Old array lenght: ".count($indexed_array);

echo "<br>";
echo "<hr>";
echo "<br>";
echo "<h2>Returened Pop item</h2>";
echo array_pop($indexed_array);

echo "<br>";
echo "<hr>";
echo "<br>";
echo "<h2>New Array list  after Poping item</h2>";
print_r($indexed_array);
echo "New array lenght: ",count($indexed_array);
?>