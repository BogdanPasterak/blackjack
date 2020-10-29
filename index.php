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
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // first time false
    $visited = isset($_SESSION['visited']);
    // next true
    $_SESSION['visited'] = true;

    // Model
    // variable

    // Controller
    // echo "Visited ".rand(1,3);


    function fillRandomDeck() {
        $fullDeck = array();
        $randomDeck = array();
        for ($i=0; $i < 52; $i++) { 
            $fullDeck[$i] = $i + 1;
        }
        for ($i=0; $i < 52; $i++) {
            $rnd = rand(0, count($fullDeck) - 1);
            // if ($i < 20) print($rnd.",[".$fullDeck[$rnd]."] ");
            $randomDeck[$i] = $fullDeck[$rnd];
            array_splice($fullDeck, $rnd, 1);
        }
        // print_r($randomDeck);
        return $randomDeck;
    }


    if (! $visited) {
        $_SESSION["pointer"] = 0;
    }
    else {
        if (! isset($_SESSION["deck"])) {
            session_destroy();
            echo "Destroy Session";
        }
    }


    echo "<h1>Bogdan Pasterak L00157106</h1>";
    // View

    if (isset($_GET['submit'])) {
        switch ($_GET['submit']) {
            case 'DEAL':
                echo 'DEAL ';
                $_SESSION["deck"] = fillRandomDeck();
            break;
            case 'married':
                echo 'you are married';
            break;
            case 'divorced':
                echo 'you are divorced';
            break;
            default:
                echo 'you are something else';
        }
    }

    // echo "<p>Deck ".count($_SESSION["deck"])." el ".$_SESSION["pointer"]." = ".
    //             $_SESSION["deck"][$_SESSION["pointer"]]."</p>";

?>
<div class="play">
    <div class="top">
        <fieldset class="quarter">
            <legend>Dealer</legend>
        </fieldset>
        <fieldset class="quarter">
            <legend>Deck</legend>
        </fieldset>
    </div>
    <div class="player">
        <fieldset>
            <legend>Player</legend>
        </fieldset>
    </div>

</div>
<form action="index.php" method="get">
    <fieldset>
        <legend>Game Action</legend>
        <input type="range" name="value" id="rangeCoin" min="2" max="20" onmousemove="showValue(this.value)"
            onmousedown="showValue(this.value)" onmouseup="showValue(this.value)" value="2">
        <input type="number" name="v" id="numberCoin" min="2" max="20" step="1" width="3"
            onchange="setRange(this.value)" value="2" style="width: 1em;">
        <input type="submit" name="submit" value="DEAL">
        <input type="submit" name="submit" value="HIT">
        <input type="submit" name="submit" value="STAND">
        <input type="submit" name="submit" value="DOUBLE">
        <input type="submit" name="submit" value="SPLIT">
        </fieldset>
    <fieldset class="grow-half">
    <legend>Reset Game</legend>
        <input type="submit" name="submit" value="RESET">
    </fieldset>
</form>
    
<?php
    // print_r("Visited: ".$_SESSION["visited"]."<br>");
    // print_r("Deck: ".count($_SESSION["deck"])."<br>");
    // print_r("Pointer: ".$_SESSION["pointer"]."<br>");
?>
<script>
    function showValue(val) 
    {   coin = document.querySelector("#numberCoin").value = val; }
    function setRange(val)
    {   coin = document.querySelector("#rangeCoin").value = val; }
</script>
</body>
</html>