<?php

require_once "./Car.php";
/*

The {extends} key word is followed by a Parent class, thereby inheriting all properties(variables) and protected and public methods(functions)

*/
class Dealership extends Car{
    private $salesPerson;
    private $age;
    public $showroom;
    public static $deals_count;

    public function __construct($salesPerson, $age){
        $this->salesPerson = $salesPerson;
        $this->age = $age;
        $this::$deals_count++;
    }

    public function setCarInfo($brand, $price, $color){
        $this->brand = $brand;
        $this->price = $price;
        $this->color = $color;
    }

    public function setBuyer($buyer_name){
        $this->buyer = $buyer_name;
    }

    public function getCarAndDealerRecord(){
        return "
            <h1>Negotiation Record</h1> <hr>
            <h2>Dealership Infomation</h2>
            Sales Person: $this->salesPerson <br>
            Sales Person's age: $this->age <br>
            Show Room: $this->showroom <br> <hr>

            <h2>Car's Information</h2> <hr>
            Brand: $this->brand <br>
            Price: $this->price <br>
            Color: $this->color <br> <hr>

            <h2>Buyer's Information</h2> <hr>
            Buyer's name: $this->buyer <br>
            
            <h2>Buyer's Information</h2> <hr>
            Total deals: {$this::$deals_count} <br>
        ";
    }
}