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
    // first time false (semafor)
    $visited = isset($_SESSION['visited']);
    // next true
    $_SESSION['visited'] = true;

    // Model
    // variable

    function continueGame() {
        $_SESSION["playerCards"] = array();
        $_SESSION["dealerCards"] = array();
        $_SESSION["dealerVisable"] = false;
        $_SESSION["message"] = "<h1>WELCOME</h1><h2>Do you want to play Blackjack<h2>";
        $_SESSION["modalVisable"] = " invisible";
        $_SESSION["bet"] = 0;
        $_SESSION["game"] = "";
        $_SESSION["buttons"] = array(
            "deal" => "",
            "hit" => "disabled",
            "stand" => "disabled",
            "double" => "disabled",
        );
    }

    function resetGame() {
        $_SESSION["money"] = 10;
        $_SESSION["result"] = array(
            "dealer" => 0,
            "player" => 0,
            "draw" => 0,
        );
        continueGame();
    }

    // Controller

    function customError($errno, $errstr) {
        echo "<h2>Something went wrong</h2>";
        echo "<h3>Error: [$errno] $errstr </h3>";
        unset($_SESSION['visited']);
        unset($visited);
        session_destroy();
        die();
    }

    // Error hendling
    set_error_handler("customError");

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

    function getCard() {
        return array_splice($_SESSION["deck"], 0, 1)[0];
    }

    function dealCards() {
        $_SESSION["bet"] = $_GET["value"];
        $_SESSION["deck"] = fillRandomDeck();
        $_SESSION["money"] -= $_GET["value"];
        $_SESSION["buttons"]["deal"] = "disabled";
        $_SESSION["buttons"]["hit"] = "";
        $_SESSION["buttons"]["stand"] = "";
        if ($_SESSION["money"] >= $_SESSION["bet"])
            $_SESSION["buttons"]["double"] = "";

        array_push($_SESSION["playerCards"], getCard());
        array_push($_SESSION["dealerCards"], getCard());
        array_push($_SESSION["playerCards"], getCard());
        array_push($_SESSION["dealerCards"], getCard());
    }

    function hitCard()
    {
        $_SESSION["buttons"]["double"] = "disabled";
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
        $_SESSION["buttons"]["double"] = "disabled";
        $_SESSION["dealerVisable"] = true;
        $playerScore = scoring("player");
        $dealerScore = scoring("dealer");
        while ($dealerScore <= 16)
        {
            array_push($_SESSION["dealerCards"], getCard());
            $dealerScore = scoring("dealer");
        }
        

        if ($dealerScore == $playerScore)
            $_SESSION["game"] = "draw";
        elseif ($dealerScore > 21 or $dealerScore < $playerScore )
            $_SESSION["game"] = "won";
        else
            $_SESSION["game"] = "lost";
    }

    function doubleBet()
    {
        $_SESSION["money"] -= $_SESSION["bet"];
        $_SESSION["bet"] *= 2;
        hitCard();
    }

    function won()
    {
        $_SESSION["result"]['player'] += 1;
        $_SESSION["money"] += $_SESSION["bet"] * 2;
        $_SESSION["message"] = "YOU WON ".($_SESSION["bet"] * 2)." €";
    }

    function lost()
    {
        $_SESSION["result"]['dealer'] += 1;
        $_SESSION["message"] = "YOU LOST ".$_SESSION["bet"]." €";
    }

    function draw()
    {
        $_SESSION["result"]['draw'] += 1;
        $_SESSION["money"] += $_SESSION["bet"];
        $_SESSION["message"] = "YOU DRAW";
    }

    function limit() {
        return max(2, min(20, $_SESSION["money"]));
    }

    // first start, set data and svow welcome
    if (! $visited) {
        resetGame();
        $_SESSION["modalVisable"] = "";
    }

    // main loop, controll data and set view

    if (isset($_GET['submit'])) {
        switch ($_GET['submit']) {
            case 'DEAL':
                dealCards();
            break;
            case 'HIT':
                hitCard();
                if (scoring("player") > 21 )
                {
                    lost();
                    $_SESSION["modalVisable"] = "";
                }
            break;
            case 'DOUBLE':
                doubleBet();
                if (scoring("player") > 21 )
                {
                    lost();
                    $_SESSION["modalVisable"] = "";
                    break;
                }
                // no break ! continue with stand
            case 'STAND':
                dealerHit();
                switch($_SESSION["game"]) {
                    case "won":
                        won();
                    break;
                    case "lost":
                        lost();
                    break;
                    default:
                        draw();
                }
                $_SESSION["modalVisable"] = "";
                $_SESSION["game"] = "";
            break;
            case 'CONTINUE':
                continueGame();
                if ($_SESSION["money"] < 2)
                {
                    $_SESSION["message"] = "You've lost all your money !!<br>Find money and come back.";
                    $_SESSION["modalVisable"] = "";
                }
            break;
            case 'RESET':
                resetGame();
            break;
            case 'HELP':
                $_SESSION["modalVisable"] = "";
                $_SESSION["message"] = "HELP";
            break;
            case 'BACK':
                $_SESSION["modalVisable"] = " invisible";
            break;
            default:
                echo $_GET['submit'];
        }
    }

    // View

?>

<h1>BLACKJACK by Bogdan Pasterak L00157106</h1>
<div class="result">
    <h2>Dealer : <?php echo $_SESSION["result"]["dealer"] ?></h2>
    <h2>Draw : <?php echo $_SESSION["result"]["draw"] ?></h2>
    <h2>Player : <?php echo $_SESSION["result"]["player"] ?></h2>
</div>
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

// to look at the card on top deck
$topDeck = ( ! empty($_SESSION["deck"]) ) ? $_SESSION["deck"][0] : '1';

?>
        </fieldset>
        <fieldset class="right center">
            <legend>Deck</legend>
            <!-- hovering the mouse turns the card -->
            <div>
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <img src="images/back.png" alt="" class="flip-card-img">
                        </div>
                        <div class="flip-card-back">
                            <img src="images/<?php echo $topDeck ?>.png" alt="" class="flip-card-img">
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    <div class="player">
        <fieldset class="center">
            <legend>Player budget: <b><?php echo number_format($_SESSION["money"], 2) ?> </b></legend>
            <div class="info">
                <h3>Scoring : <?php echo scoring("player") ?></h3>
                <h3>Bet: <?php echo $_SESSION["bet"] ?></h3>
            </div>
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
        <legend>Game Action -- Set Bet</legend>
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
        </fieldset>
    <fieldset class="grow-half">
    <legend>Controll Game</legend>
        <input type="submit" name="submit" value="HELP">
        <input type="submit" name="submit" value="RESET">
    </fieldset>
</form>

<form class="modal<?php echo ($_SESSION["modalVisable"]); ?>" action="index.php" method="get">
    <h2><?php echo $_SESSION["message"] ?></h2>
<?php if ($_SESSION["message"] == "HELP") : ?>
    <div class="help">
        <p>The goal of blackjack is to beat the dealer's hand</p>
        <p>without going over 21.</p>
        <div class="points">
            <div class="cards">
                <div class="fiew">
                    <img src="images/14.png" alt="" class="img-mini">
                    <p>Aces are worth 1 or 11, whichever makes a better hand.</p>
                </div>
                <div class="fiew">
                    <img src="images/3.png" alt="" class="img-mini">
                    <img src="images/33.png" alt="" class="img-mini">
                    <img src="images/18.png" alt="" class="img-mini">
                    <p>Number cards are worth number.</p>
                </div>
                <div class="fiew">
                    <img src="images/12.png" alt="" class="img-mini">
                    <img src="images/37.png" alt="" class="img-mini">
                    <img src="images/52.png" alt="" class="img-mini">
                    <p>Face cards are worth 10.</p>
                </div>
            </div>
        </div>
    </div>
        <input type="submit" name="submit" value="BACK" class="reset-btn">
<?php elseif (strlen($_SESSION["message"]) > 20) : ?>
    <input type="submit" name="submit" value="RESET" class="reset-btn">
<?php else : ?>
    <input type="submit" name="submit" value="CONTINUE" class="reset-btn">
<?php endif ; ?>
</form>
    
<script>
    function showValue(val) 
    {   coin = document.querySelector("#numberCoin").value = val; }
    function setRange(val)
    {   coin = document.querySelector("#rangeCoin").value = val; }
</script>
</body>
</html>