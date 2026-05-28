<?php
session_start();
$_SESSION["title"] = "Eredmények";
$oldal = "eredmenyek";
include('../includes/overall/header.php');
include('../includes/overall/db_connect.php');
?>

<div id="szuro-blokk">
    <form method="GET" id="szuroForm" class="szuro-container">
    <?php if(isset($_SESSION["rang"]) && $_SESSION["rang"] == "diak"): ?>
        <label for="feladatsor">Feladatsor:</label>
        <select name="feladatsor" id="feladatsor" onchange="document.getElementById('szuroForm').submit();">
            <option value="">Összes</option>
            <?php
            $sqlFel = "SELECT id, nev FROM feladatsorok ORDER BY nev ASC";
            $resultFel = $conn->query($sqlFel);
            while($fs = $resultFel->fetch_assoc()) {
                $selected = (isset($_GET['feladatsor']) && $_GET['feladatsor'] == $fs['id']) ? "selected" : "";
                echo "<option value='" . $fs['id'] . "' $selected>" . htmlspecialchars($fs['nev']) . "</option>";
            }
            ?>
        </select>
    <?php elseif(isset($_SESSION["rang"]) && $_SESSION["rang"] == "tanar"): ?>
    <label for="feladatsor">Feladatsor:</label>
    <select name="feladatsor" id="feladatsor" onchange="document.getElementById('szuroForm').submit();">
        <option value="">Válassz feladatsort</option>
        <?php
        $sqlFeladatsor = "SELECT id, nev FROM feladatsorok ORDER BY nev ASC";
        $feladatsorok = $conn->query($sqlFeladatsor);
        while($fs = $feladatsorok->fetch_assoc()) {
            $sel2 = (isset($_GET['feladatsor']) && $_GET['feladatsor'] == $fs['id']) ? "selected" : "";
            echo "<option value='".$fs['id']."' $sel2>".htmlspecialchars($fs['nev'])."</option>";
        }
        ?>
    </select>

    <label for="csoport">Csoport:</label>
    <select name="csoport" id="csoport" onchange="document.getElementById('szuroForm').submit();">
        <option value="">Összes csoport</option>
        <?php
        $sqlCsoport = "SELECT id, nev FROM csoportok WHERE oktato_id = ".$_SESSION["felhasznalo"];
        $csoportok = $conn->query($sqlCsoport);
        while($csoport = $csoportok->fetch_assoc()) {
            $sel = (isset($_GET['csoport']) && $_GET['csoport'] == $csoport['id']) ? "selected" : "";
            echo "<option value='".$csoport['id']."' $sel>".htmlspecialchars($csoport['nev'])."</option>";
        }
        ?>
    </select>

    <label for="kereses">Név:</label>
    <input type="text" id="kereses">
<?php endif; ?>
    </form>
</div>

<div id="eredmenyek">
<?php
if(isset($_SESSION["rang"]) && $_SESSION["rang"]=="diak") {
    $sql = "SELECT * FROM eredmenyek WHERE diak_id=".$_SESSION["felhasznalo"];
    if(isset($_GET['feladatsor']) && $_GET['feladatsor'] != '') {
        $sql .= " AND feladatsor_id=".intval($_GET['feladatsor']);
    }
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $sql2 = "SELECT * FROM feladatsorok WHERE id=".$row["feladatsor_id"];
            $result2 = $conn->query($sql2);
            $row2 = $result2->fetch_assoc();
            echo "<div class='eredmeny'>
                    <div class='fejlec'><h3>".htmlspecialchars($row2["nev"])."</h3></div>
                    <hr>
                    <p>Kitöltés időpontja: ".htmlspecialchars($row["datum"])."</p>
                    <p>Elért pontszám: ".intval($row["pontszam"])."</p>
                    <p>Százalékban: ".floatval($row["szazalek"])."%</p>
                  </div>";
        }
    } else {
        echo "<p>Nincs eredmény.</p>";
    }
}

elseif(isset($_SESSION["rang"]) && $_SESSION["rang"]=="tanar") {

    if(!empty($_GET['feladatsor'])) {
        $feladatsor_id = intval($_GET['feladatsor']);
        $csoport_id = !empty($_GET['csoport']) ? intval($_GET['csoport']) : null;

        $sqlEredm = "
            SELECT e.*, f.nev AS feladatsor_nev, u.nev AS diak_nev, u.azonosito AS diak_azonosito
            FROM eredmenyek e
            JOIN feladatsorok f ON e.feladatsor_id = f.id
            LEFT JOIN felhasznalok u ON e.diak_id = u.id
            WHERE e.feladatsor_id = $feladatsor_id
        ";
        if($csoport_id) {
            $sqlEredm .= "
                AND e.diak_id IN (
                    SELECT felhasznalo_id FROM csoporttagok WHERE csoport_id = $csoport_id
                )
            ";
        } else {
            $sqlEredm .= "
                AND (
                    e.diak_id IN (
                        SELECT ct.felhasznalo_id
                        FROM csoporttagok ct
                        JOIN csoportok c ON c.id = ct.csoport_id
                        WHERE c.oktato_id = ".$_SESSION["felhasznalo"]."
                    )
                    OR e.diak_id NOT IN (SELECT id FROM felhasznalok)
                )
            ";
        }

        $sqlEredm .= " ORDER BY e.datum DESC";
        $eredmenyek = $conn->query($sqlEredm);

        if($eredmenyek && $eredmenyek->num_rows > 0) {
            while($row = $eredmenyek->fetch_assoc()) {
                if ($row["diak_nev"]) {
                    $azon = $row["diak_azonosito"] ?: $row["diak_id"];
                    $nev = htmlspecialchars($azon)." - ".htmlspecialchars($row["diak_nev"]);
                } else {
                    $nev = "Vendég – ".htmlspecialchars($row["diak_id"]);
                }

                echo "<div class='eredmeny'>
                        <div class='fejlec'>
                            <h3>$nev - ".htmlspecialchars($row["feladatsor_nev"])."</h3>
                        </div>
                        <hr>
                        <p>Kitöltés időpontja: ".htmlspecialchars($row["datum"])."</p>
                        <p>Elért pontszám: ".intval($row["pontszam"])."</p>
                        <p>Százalékban: ".floatval($row["szazalek"])."%</p>
                      </div>";
            }
        } else {
            echo "<p>Nincs eredmény a kiválasztott feladatsorra.</p>";
        }
    } else {
        echo "<p>Válassz feladatsort az eredmények megjelenítéséhez.</p>";
    }
}

?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const keresInput = document.getElementById("kereses");
    if (keresInput) {
        keresInput.addEventListener("keyup", function() {
            const filter = keresInput.value.toLowerCase();
            const eredmenyek = document.querySelectorAll(".eredmeny");

            eredmenyek.forEach(div => {
                const nev = div.querySelector(".fejlec h3")?.textContent.toLowerCase() || "";
                if (nev.includes(filter)) {
                    div.style.display = "block";
                } else {
                    div.style.display = "none";
                }
            });
        });
    }
});
</script>

<?php
include('../includes/overall/footer.php');
include('../includes/overall/db_disconnect.php');
?>