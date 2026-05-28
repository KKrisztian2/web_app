<?php 
session_start();
$oldal = "csoport_letrehoz";
$_SESSION["title"] = "Csoportok";
include('../includes/overall/header.php');
include('../includes/overall/db_connect.php'); 
?>

<div id="letezo_csoportok">
<?php
    if (isset($_SESSION["rang"]) && $_SESSION["rang"] == "tanar") {
        $sql = "SELECT id, nev FROM csoportok WHERE oktato_id = " . $_SESSION["felhasznalo"];
        $result = $conn->query($sql);
        while ($csoport = $result->fetch_assoc()) {
            echo '<div class="csoport tanar" data-id="' . $csoport["id"] . '" data-nev="' . htmlspecialchars($csoport["nev"]) . '">';
            echo '<h3>' . htmlspecialchars($csoport["nev"]) . '</h3>';
            echo '<ul class="megjeleno">';

            $sql2 = "SELECT f.id, f.nev, f.azonosito 
                     FROM felhasznalok f 
                     JOIN csoporttagok ct ON f.id = ct.felhasznalo_id 
                     WHERE ct.csoport_id = " . $csoport["id"];
            $res2 = $conn->query($sql2);
            while ($tag = $res2->fetch_assoc()) {
                $azon = htmlspecialchars($tag["azonosito"]);
                $nev  = htmlspecialchars($tag["nev"]);
                echo '<li data-id="' . $tag["id"] . '">' . $azon . ' - ' . $nev . '</li>';
            }

            echo '</ul>';
            echo '</div>';
        }
    } else if (isset($_SESSION["rang"]) && $_SESSION["rang"] == "diak") {
        $sql = "SELECT c.id, c.nev FROM csoportok c 
                JOIN csoporttagok ct ON c.id = ct.csoport_id 
                WHERE ct.felhasznalo_id = " . $_SESSION["felhasznalo"];
        $res = $conn->query($sql);
        while ($csoport = $res->fetch_assoc()) {
            echo '<div class="csoport diak">';
            echo '<h3>' . htmlspecialchars($csoport["nev"]) . '</h3>';
            echo '<ul class="megjeleno">';

            $sql2 = "SELECT f.id, f.nev, f.azonosito 
                     FROM felhasznalok f 
                     JOIN csoporttagok ct ON f.id = ct.felhasznalo_id 
                     WHERE ct.csoport_id = " . $csoport["id"];
            $res2 = $conn->query($sql2);
            while ($tag = $res2->fetch_assoc()) {
                $azon = htmlspecialchars($tag["azonosito"]);
                $nev  = htmlspecialchars($tag["nev"]);
                echo '<li data-id="' . $tag["id"] . '">' . $azon . ' - ' . $nev . '</li>';
            }

            echo '</ul>';
            echo '</div>';
        }
    }
?>  
</div>

<?php if (isset($_SESSION["rang"]) && $_SESSION["rang"] == "tanar"): ?>
    <button id="alap_gomb">Csoport létrehozása</button>
<?php endif; ?>

<div id="csoport-popup">
    <span id="bezar_gomb"><i class="fa fa-close"></i></span>
    <div id="flex">
        <div id="nevsor">
            <label for="kereses">Keresendő diák:</label><br>
            <input type="text" name="kereses" id="kereses">
            <ul>
            <?php
                $sql = "SELECT id, nev, azonosito FROM felhasznalok WHERE rang='diak'";
                $res = $conn->query($sql);
                while ($diak = $res->fetch_assoc()) {
                    $azon = htmlspecialchars($diak["azonosito"]);
                    $nev  = htmlspecialchars($diak["nev"]);
                    echo '<li data-id="' . $diak["id"] . '">' . $azon . ' - ' . $nev . '</li>';
                }
            ?>
            </ul>
        </div>

        <div id="gombok">
            <button id="bedob"><i class="fa fa-arrow-right"></i></button><br>
            <button id="kidob"><i class="fa fa-arrow-left"></i></button>
        </div>

        <div id="csoport">
            <label for="csoportnev">Csoport neve:</label><br>
            <input type="text" name="csoportnev" id="csoportnev">
            <ul></ul>
        </div>

        <div>
            <button class="alap_gomb" id="letrehoz">Létrehoz</button>
        </div>
    </div>
</div>

<div id="uzenet" style="display:none;"></div>

<script src="csoportletrehoz.js"></script>

<?php 
include('../includes/overall/footer.php'); 
include('../includes/overall/db_disconnect.php');
?>
