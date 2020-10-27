<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blackjack</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
    // Model
    // variable
    $deck;

    function fillRandomDeck() {
        for ($i=0; $i < 52; $i++) { 
            $deck[$i] = $i;
        }
    }

    function init() {
        $deck = array();
        fillRandomDeck();

    }

    // Controller
    // start 
    if (!isset($deck)) {
        init();
    }

?>
    <h1>Bogdan Pasterak L00157106</h1>
    
<?php
    // View
    echo "<p>Deck {$deck}</p>";

?>
</body>
</html>