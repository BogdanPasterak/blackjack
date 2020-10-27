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
        $randomDeck = array();
        for ($i=0; $i < 52; $i++) { 
            $randomDeck[$i] = $i;
        }
        return $randomDeck;
    }

    function init() {
        global $deck;
        $deck = fillRandomDeck();
    }

    // Controller
    // start 
    if (!isset($deck)) {
        init();
    }


    echo "<h1>Bogdan Pasterak L00157106</h1>";
    // View
    // echo "<p>Deck ".count($deck)." el 3 = ".$deck[2]."</p>";
    if (isset($_GET['cli'])) {
        echo "cll button clicked";
    }

?>

<form action="index.php" method="get">
    <input type="submit" name="cli" value="Sto">
</form>
    
</body>
</html>