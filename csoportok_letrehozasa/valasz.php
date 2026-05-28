<?php 
session_start();
include('../includes/overall/db_connect.php');

if (empty($_POST["csoportnev"])) {
    exit("Hiba: nincs megadva csoportnév!");
}

$csoportnev = $_POST["csoportnev"];
$diakok = $_POST["diakok"] ?? [];
$csoport_id = $_POST["csoport_id"] ?? null;

if ($csoport_id) {
    $stmt = $conn->prepare("UPDATE csoportok SET nev=? WHERE id=?");
    $stmt->bind_param("si", $csoportnev, $csoport_id);
    $stmt->execute();
    $stmt->close();

    $conn->query("DELETE FROM csoporttagok WHERE csoport_id = " . intval($csoport_id));

    foreach ($diakok as $id) {
        $stmt = $conn->prepare("INSERT INTO csoporttagok (felhasznalo_id, csoport_id) VALUES (?,?)");
        $stmt->bind_param("ii", $id, $csoport_id);
        $stmt->execute();
        $stmt->close();
    }

    echo "Csoport módosítva!";
} else {
    $stmt = $conn->prepare("INSERT INTO csoportok (oktato_id, nev) VALUES (?, ?)");
    $stmt->bind_param("is", $_SESSION["felhasznalo"], $csoportnev);
    $stmt->execute();
    $new_id = $stmt->insert_id;
    $stmt->close();

    foreach ($diakok as $id) {
        $stmt = $conn->prepare("INSERT INTO csoporttagok (felhasznalo_id, csoport_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $id, $new_id);
        $stmt->execute();
        $stmt->close();
    }

    echo "Csoport létrehozva!";
}

include('../includes/overall/db_disconnect.php');
?>
