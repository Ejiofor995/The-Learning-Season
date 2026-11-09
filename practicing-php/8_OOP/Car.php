<!-- 
    Note that i capitalized the class file name Car.php 

    1) A class is a blue print that is used to create same product of multiple times and allows variance like a 3D printer that can make blue, red, and various colors of the same toy car.

    2) A class have properties, __Objects__ and methods, we can define the eccessibility scope of each by prefixing them with any of these three accessibility scopes, just like defining a variable in javascript with const and let
        -private(Only acccessible to the current class)---------Personal
        -protected(Only acccessible to the current class and any child class that extends it)-----------Relatives
        -public(Accessible to all)--------------Everyone
    
    3) A good practice is to set all properties and methods to {private} then gradually set the ones you need to access puplicly to {public} or if you need them accessible to children that extends the class then set them to {protected}

    4) We usually import this class file into other php files then make more copies(objects) from this blueprint(class)

    5) "->" is used to access a property inside of a class only if that property is made public, if it is private(personal) or protected(relative) then this wont work.
 -->

<?php

class Car{
    
    // Defining personal properties(Variables)
    protected $brand;
    protected $color;
    protected $price;
    public $buyer;

    // Defining a constructor, this assigns properties(variables) to the classses parameters, so the parameters we see in the function are not same thing as the properties we defined above, but it's best practice to give them same name. 
    // You can also provide default values so if this class is not supplied any parameter then it will fall back to the default values you assigned to the parameters so it basically works like a function. Parameters without default values needs be defined before those with default values.
    public function __construct($price_param,$brand_param='default brand', $color_param='default color'){
        #$this->property = class parameter
        $this->price = $price_param;
        $this->brand = $brand_param;
        $this->color = $color_param;

    }

    // Creating methods, these are like machines in an industry that performs different tasks, the class itself is the industry in this case, each method can access any properties(variables) in this class using $this->the_property's_name; no need to pass it in as a parameter
    // Methods are basically functions and are usually made public
    public function generateReceipt(){
        return "
            <h2>Purchase Summary: </h2><hr>
            Brand: $this->brand <br>
            Color: $this->color <br>
            Price: $this->price <br>
            Buyer: $this->buyer <br>
            <hr>
            <center>
                <h2>---Sold🚗---</h2>
            </center>
            <hr>
        ";
    }     



    public function setBuyer($buyer_param){
        $this->buyer = $buyer_param;
    }
    
}


echo "Go to the receipt page: <a href='./receipt.php'>http://localhost/practicing-php/8_OOP/index.php </a>";

echo "Go to the record page: <a href='./record.php'>http://localhost/practicing-php/8_OOP/record.php </a>";