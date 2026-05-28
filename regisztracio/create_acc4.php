<?php
session_start();
$_SESSION["title"] = "Regisztráció";
$oldal = "login";
include('../includes/overall/header.php');

$azonosito = "";
$hibak = [];

if (isset($_SESSION["rang"]) && $_SESSION["rang"] == "tanar" && isset($_SESSION["new_rang"])) {
    if (isset($_POST["regisztracio"])) {
        $azonosito = megtisztit($_POST["azonosito"]);

        if (empty($azonosito)) {
            $hibak["azonosito"] = "Töltse ki a mezőt.";
        }

        if (!preg_match("/^[A-Za-z0-9_-]+$/", $azonosito)) {
            $hibak["azonosito"] = "Az azonosító csak betűket, számokat, aláhúzást vagy kötőjelet tartalmazhat.";
        }

        if (count($hibak) == 0) {
            $_SESSION["new_azonosito"] = $azonosito;
            $azonosito = "";
            header("Location: regisztracio.php");
            exit();
        }
    }
} else {
    header("Location: create_acc3.php");
    exit();
}

function megtisztit($adat) {
    $adat = trim($adat);
    $adat = stripslashes($adat);
    $adat = htmlspecialchars($adat);
    return $adat;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Regisztráció</title>
    <meta charset="utf-8">
</head>
<body>
    <div id="doboz">
        <?php
        if (isset($hibak["azonosito"])) {
            echo "<div id='hiba'>";
            echo $hibak["azonosito"];
            echo '<button id="bezar" onclick="bezar()">x</button>';
            echo "</div>";
        }
        ?>
    </div>

    <form action="" method="post">
        <div id="fejlec">
            <h2>Regisztráció</h2>
            <h3>Regisztráljon egy új fiókot</h3>
        </div>
        <input type="text" name="azonosito" value="<?php echo $azonosito ?>" autocomplete="off" placeholder="Azonosító"><br>
        <input type="submit" name="regisztracio" value="Regisztrálás">
    </form>

    <script src="login.js"></script>
</body>
</html>

<?php
include('../includes/overall/footer.php');
?>
