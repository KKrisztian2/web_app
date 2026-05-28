<?php
session_start();
$_SESSION["error"] = false;
include('../includes/overall/db_connect.php');

if (isset($_POST["submit"])) {
    if (!empty($_POST["szoveg"])) {

        $adatok_megfeleloek = adatokMegtisztitasaEllenorzese($conn);

        if ($adatok_megfeleloek) {
            $helyes_valasz = "";
            $valaszok_szama = 0;
            valaszKodolasa($helyes_valasz, $valaszok_szama);

            $pont = intval($_POST["pont"]);
            if ($pont < 0) $pont = 0;

            if (isset($_SESSION["edit_task_id"])) {
                $sql = "UPDATE feladatok SET 
                            szoveg = ?, 
                            tipus = ?, 
                            valaszok_szama = ?, 
                            megoldas = ?, 
                            temakor = ?, 
                            nyilvanos = ? 
                        WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("siisssi", 
                    $_POST["szoveg"], 
                    $_POST["tipus"], 
                    $valaszok_szama, 
                    $helyes_valasz, 
                    $_POST["temakor"], 
                    $_POST["nyilvanos"], 
                    $_SESSION["edit_task_id"]
                );
                $stmt->execute();
                $stmt->close();

                $sql = "UPDATE kerdes_feladat SET pont = ? WHERE feladatsor_id = ? AND kerdes_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iii", $pont, $_GET["feladatsor"], $_SESSION["edit_task_id"]);
                $stmt->execute();
                $stmt->close();

            } else {
                $sql = "INSERT INTO feladatok (szoveg, tipus, valaszok_szama, megoldas, temakor, nyilvanos) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("siisss", 
                    $_POST["szoveg"], 
                    $_POST["tipus"], 
                    $valaszok_szama, 
                    $helyes_valasz, 
                    $_POST["temakor"], 
                    $_POST["nyilvanos"]
                );
                if (!$stmt->execute()) {
                    $_SESSION["error"] = true;
                    $_SESSION["error_message"] = "Hiba a feladat mentésekor!<br>" . $conn->error;
                } else {
                    $id = $conn->insert_id;
                    $sql = "INSERT INTO kerdes_feladat (feladatsor_id, kerdes_id, pont) VALUES (?, ?, ?)";
                    $stmt2 = $conn->prepare($sql);
                    $stmt2->bind_param("iii", $_GET["feladatsor"], $id, $pont);
                    $stmt2->execute();
                    $stmt2->close();

                    $_SESSION["edit_task_id"] = $id;
                }
                $stmt->close();
            }

            if (!$_SESSION["error"]) {
                $_SESSION["mesage"] = "Mentés sikeres!";
            }
        } else {
            $_SESSION["error"] = true;
            $_SESSION["error_message"] = "Érvénytelen vagy hiányos adatok!";
        }
    } else {
        $_SESSION["error"] = true;
        $_SESSION["error_message"] = "Nem hozott létre feladatot!";
    }

    if (isset($_SESSION["edit_task_id"])) {
        $sql = "SELECT f.*, k.pont FROM feladatok f 
                LEFT JOIN kerdes_feladat k ON k.kerdes_id = f.id AND k.feladatsor_id = ? 
                WHERE f.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $_GET["feladatsor"], $_SESSION["edit_task_id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $_SESSION["edit_task"] = $result->fetch_assoc();
        }
        $stmt->close();
    }

    header("Location: index.php?feladatsor=" . $_GET["feladatsor"]);
    exit();
}

function adatokMegtisztitasaEllenorzese($conn) {
    function megtisztit($adat) {
        $adat = trim($adat);
        $adat = stripslashes($adat);
        $adat = htmlspecialchars($adat);
        return $adat;
    }

    $_POST["temakor"] = megtisztit($_POST["temakor"]);
    $_POST["nyilvanos"] = megtisztit($_POST["nyilvanos"]);
    $_POST["pont"] = intval($_POST["pont"]);

    if (!in_array($_POST["nyilvanos"], ["1", "2"])) {
        return false;
    }

    if ($_POST["pont"] < 0) return false;

    if ($_POST["tipus"] == "1") {
        $valaszok = ["A","B","C","D","E"];
        return in_array($_POST["helyes_teszt"], $valaszok);
    } 
    else if ($_POST["tipus"] == "2") {
        $nevek = ["elso","masodik","harmadik","negyedik"];
        $valaszok = ["A","B","C","D","E"];
        foreach ($nevek as $nev) {
            if (!in_array($_POST[$nev], $valaszok)) return false;
        }
    } 
    else if ($_POST["tipus"] == "3") {
        $_POST["helyes1"] = megtisztit($_POST["helyes1"]);
        if ($_POST["db"] > 1) $_POST["helyes2"] = megtisztit($_POST["helyes2"]);
        if ($_POST["db"] > 2) $_POST["helyes3"] = megtisztit($_POST["helyes3"]);
    }

    return true;
}

function valaszKodolasa(&$helyes_valasz, &$valaszok_szama) {
    if ($_POST["tipus"] == "1") {
        $helyes_valasz = $_POST["helyes_teszt"];
        $valaszok_szama = 1;
    } else if ($_POST["tipus"] == "2") {
        $helyes_valasz = $_POST["elso"] . "\t" . $_POST["masodik"] . "\t" . $_POST["harmadik"] . "\t" . $_POST["negyedik"];
        $valaszok_szama = 4;
    } else if ($_POST["tipus"] == "3") {
        $helyes_valasz = $_POST["helyes1"];
        $valaszok_szama = 1;
        if ($_POST["db"] > 1) {
            $helyes_valasz .= "\t" . $_POST["helyes2"];
            $valaszok_szama++;
        }
        if ($_POST["db"] > 2) {
            $helyes_valasz .= "\t" . $_POST["helyes3"];
            $valaszok_szama++;
        }
    }
}

include('../includes/overall/db_disconnect.php');
?>
