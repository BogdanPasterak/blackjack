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
            $randomDeck[$i] = $fullDeck[$rnd];
            array_splice($fullDeck, $rnd, 1);
        }
        return $randomDeck;
    }

    function resetGame() {
        $_SESSION["money"] = 10;
        $_SESSION["playerCards"] = array();
        $_SESSION["dealerCards"] = array();
        $_SESSION["dealerVisable"] = false;
        $_SESSION["message"] = "YOU WON";
        $_SESSION["modalVisable"] = "";//" invisible";
        $_SESSION["buttons"] = array(
            "deal" => "",
            "hit" => "disabled",
            "stand" => "disabled",
            "double" => "disabled",
            "split" => "disabled",
        );
    }

    function getCard() {
        return array_splice($_SESSION["deck"], 0, 1)[0];
    }

    function dealCards() {
        array_push($_SESSION["playerCards"], getCard());
        array_push($_SESSION["dealerCards"], getCard());
        array_push($_SESSION["playerCards"], getCard());
        array_push($_SESSION["dealerCards"], getCard());
    }

    function hitCard()
    {
        array_push($_SESSION["playerCards"], getCard());
    }

    function scoring($who) {
        $sum = 0;
        $ass = 0;
        $cards = $_SESSION[$who."Cards"];
        foreach ($cards as $value) {
            $lessColor = ((int)$value - 1) % 13;
            if ($lessColor == 0)
            {
                $sum += 11;
                $ass++;
            }
            elseif ($lessColor < 10)
            {
                $sum += $lessColor + 1;
            }
            else
            {
                $sum += 10;
            }
        }
        if ($sum > 21 and $ass > 0)
        {
            $sum -= 10;
            if ($sum > 21 and $ass > 1)
            {
                $sum -= 10;
                if ($sum > 21 and $ass > 2)
                {
                    $sum -= 10;
                    if ($sum > 21 and $ass == 4)
                    {
                        $sum -= 10;
                    }
                }
            }
        }
        return $sum;
    }

    function dealerHit() 
    {
        $_SESSION["dealerVisable"] = true;
        $dealerScore = scoring("dealer");
        while ($dealerScore <= 16)
        {
            array_push($_SESSION["dealerCards"], getCard());
            $dealerScore = scoring("dealer");
        }
        echo $dealerScore;

    }

    function limit() {
        return max(2, min(20, $_SESSION["money"]));
    }

    // first start
    if (! $visited) {
        resetGame();
    }
    else {
        if (isset($_GET['submit']) and $_GET['submit'] == "RESET") {
            // echo '<h2>RESET</h2>';
            $_GET['submit'] = null;
            resetGame();
        }
    }


    if (isset($_GET['submit'])) {
        switch ($_GET['submit']) {
            case 'DEAL':
                $_SESSION["deck"] = fillRandomDeck();
                $_SESSION["money"] -= $_GET["value"];
                $_SESSION["buttons"]["deal"] = "disabled";
                $_SESSION["buttons"]["hit"] = "";
                $_SESSION["buttons"]["stand"] = "";
                dealCards();
                if ((float)$_SESSION["money"] < 2)
                {
                    echo "<h2>END GAME</h2>";
                }
            break;
            case 'HIT':
                hitCard();
                if (scoring("player") > 21 )
                {
                    echo "<h2>END GAME</h2>";
                }
            break;
            case 'STAND':
                dealerHit();
                echo 'you are divorced';
            break;
            default:
                echo 'you are something else';
        }
    }

    // View

?>

<h1>Bogdan Pasterak L00157106</h1>
<div class="play">
    <div class="top">
        <fieldset class="left center">
            <legend>Dealer</legend>
<?php # insert cards
if ($_SESSION["dealerVisable"])
{
    echo "<h3>Scoring : ".scoring('dealer')."</h3>";
}
foreach ($_SESSION["dealerCards"] as $key => $card) {
    if ($key == "0")
    {
        echo ('<img src="images/'.$card.'.png">');
    }
    else
    {
        if ($_SESSION["dealerVisable"])
        {
            echo ('<img src="images/'.$card.'.png">');
        }
        else
        {
            echo ('<img src="images/back.png">');
        }
    }
}
?>
        </fieldset>
        <fieldset class="right center">
            <legend>Deck</legend>
            <div>
                <img src="images/back.png" alt="">
            </div>
        </fieldset>
    </div>
    <div class="player">
        <fieldset class="center">
            <legend>Player budget: <?php echo number_format($_SESSION["money"], 2) ?> </legend>
            <h3>Scoring : <?php echo scoring("player") ?></h3>
<?php # insert cards
foreach ($_SESSION["playerCards"] as &$card) {
    echo ('<img src="images/'.$card.'.png">');
}
?>
        </fieldset>
    </div>

</div>
<form action="index.php" method="get">
    <fieldset>
        <legend>Game Action</legend>
        <input type="range" name="value" id="rangeCoin" min="2" value="2"
            <?php echo 'max="'.limit().'"' ?>
            onmousemove="showValue(this.value)"
            onmousedown="showValue(this.value)"
            onmouseup="showValue(this.value)">
        <input type="number" name="v" id="numberCoin" min="2" step="1" value="2"
            <?php echo 'max="'.limit().'"' ?>
            onchange="setRange(this.value)" style="width: 1em;">
        <input type="submit" name="submit" value="DEAL" <?php echo $_SESSION["buttons"]["deal"] ?> >
        <input type="submit" name="submit" value="HIT" <?php echo $_SESSION["buttons"]["hit"] ?> >
        <input type="submit" name="submit" value="STAND" <?php echo $_SESSION["buttons"]["stand"] ?> >
        <input type="submit" name="submit" value="DOUBLE" <?php echo $_SESSION["buttons"]["double"] ?> >
        <input type="submit" name="submit" value="SPLIT" <?php echo $_SESSION["buttons"]["split"] ?> >
        </fieldset>
    <fieldset class="grow-half">
    <legend>Reset Game</legend>
        <input type="submit" name="submit" value="RESET">
    </fieldset>
</form>

<form class="modal<?php echo ($_SESSION["modalVisable"]); ?>" action="index.php" method="get">
    <h2><?php echo $_SESSION["message"] ?></h2>
    <input type="submit" name="submit" value="RESET" class="reset-btn">
</form>
    
<?php
    // print_r($_SESSION["playerCards"]);
?>
<script>
    function showValue(val) 
    {   coin = document.querySelector("#numberCoin").value = val; }
    function setRange(val)
    {   coin = document.querySelector("#rangeCoin").value = val; }
</script>
</body>
</html>