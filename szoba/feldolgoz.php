<?php
	session_start();
	include('../includes/overall/db_connect.php');


	$szoba = $_POST["kod"];
    $_SESSION["azonosito"] = megtisztit($_POST["azon"]);
    $sql = "SELECT feladatsor FROM szoba WHERE id = ".$szoba;
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        include('../includes/overall/db_disconnect.php');
        header("Location: ../feladatok/diak.php?feladatsor=".$row["feladatsor"]);
        exit();
    }else{
        header("Location: ../index.php");
    }

    function megtisztit($adat) {
        $adat = trim($adat);
        $adat = stripslashes($adat);
        $adat = htmlspecialchars($adat);
        return $adat;
    }

?>