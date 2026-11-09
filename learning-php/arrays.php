  <?php
  //A list (array) of fruits
  $fruits=['Apple', 'Banana', 'Coconut', 'Pineapple'];

  //Appending a fruit to the end of the array, like a push() method in javascript
  $fruits[]='Papay';

  //Updating an array using it's index position Item cuz i misspellet Papaya at first 
  $fruits[4]='Papaya';

  //Get a single item in an array uaing it's index position
  echo "<b>{$fruits[0]}</b> is the first item on our fruit shelve in which we have a total of " . count($fruits) . " fruits.";

  //echo array_pop($fruits);

  //looping through an array to display it's individual data using foreach loop
  echo "<h2>Fruit Store!</h2>";
  foreach($fruits as $fruit){
    echo "<ul>";
    echo "<li>{$fruit}</li>";
    echo "</ul>";
  }
