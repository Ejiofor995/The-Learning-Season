<?php

// Import the class (blueprint) so we can make more objects(copies of the product)
require_once "./Car.php";

// Ussage:
    /* 
    1) "->" is used to access a property inside of a class only if that property is made public, if it is private(personal) or protected(relative) then this wont work.

    2) "new ClassName(supply parameters)" this is how a class object is created it's like making a copy of a product from an existing blueprint.

    */
$negotiation_01 = new Car('50 Million USD', 'Bugatti', 'Black'); #Created a copy of a new car following the car blueprint
$negotiation_01->setBuyer('Ejiofor'); #Accessed and edited a public property of the car blueprint
echo $negotiation_01->generateReceipt(); #Displayed a public property, can't do this if it was private or protected property