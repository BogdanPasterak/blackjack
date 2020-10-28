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
        $randomDeck = array();
        for ($i=0; $i < 52; $i++) { 
            $randomDeck[$i] = $i;
        }
        return $randomDeck;
    }


    if (! $visited) {
        $_SESSION["deck"] = fillRandomDeck();
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
        if ($_GET['submit'] == "RESET") {
            $_SESSION["pointer"] = 0;
        }
        elseif ($_GET['submit'] == "Sto") {
            $_SESSION["pointer"] = 30;
        }
        else {
            $_SESSION["pointer"]++;
        }
    }

    // echo "<p>Deck ".count($_SESSION["deck"])." el ".$_SESSION["pointer"]." = ".
    //             $_SESSION["deck"][$_SESSION["pointer"]]."</p>";

?>
<script>
    function showValue(val) 
    {
        document.querySelector("#value").innerHTML(val);
    }
</script>
<form action="index.php" method="get">
    <fieldset>
        <legend>Game Action</legend>
        <input type="range" name="value" id="val" min="2" max="20">
        <input type="number" name="v" id="value" min="2" max="20" step="1" width="3">
        <input type="submit" name="submit" value="DEAL">
        <input type="submit" name="submit" value="HIT">
        <input type="submit" name="submit" value="STAND">
        <input type="submit" name="submit" value="DOUBLE">
        <input type="submit" name="submit" value="SPLIT">
        </fieldset>
    <fieldset>
    <legend>Reset Game</legend>
        <input type="submit" name="submit" value="RESET">
    </fieldset>
</form>
    
<?php
    print_r("Visited: ".$_SESSION["visited"]."<br>");
    print_r("Deck: ".count($_SESSION["deck"])."<br>");
    print_r("Pointer: ".$_SESSION["pointer"]."<br>");
?>
</body>
</html>