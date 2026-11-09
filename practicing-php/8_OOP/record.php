<?php

require_once "./Dealership.php";

$record001 = new Dealership('Micheal',18);
$record001->showroom = 'Elite Motors';
$record001->setCarInfo('Bugatti','52 Million USD','Gray');
$record001->setBuyer('Emeka Promise Ejiofor');
echo $record001->getCarAndDealerRecord();

$record002 = new Dealership('Micheal',18);
$record002->showroom = 'Elite Motors';
$record002->setCarInfo('Bugatti','52 Million USD','Gray');
$record002->setBuyer('Emeka Promise Ejiofor');
echo $record002->getCarAndDealerRecord();

// static properties and methods belong to the class directly and when a new object(copy) is made, it can't be copied, it is accessed using {::} instead of {->}. You can use it to count all copies that has been created.
// It is created by adding the {static} keyword right after definging if the method or property is {private, protected or public} then you can set your function(method) or variable(property)