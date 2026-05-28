<?php 
	$oldal = "szerkesztofeladatok";
	session_start();
	$_SESSION["title"] = "Feladatok";
	include('../includes/overall/header.php');
	include('../includes/overall/db_connect.php');
	if(isset($_SESSION["rang"]) && $_SESSION["rang"] != "tanar") header('Location: ../index.php');
 ?>
	<div class = "feladatok">
	<?php 
	if(!isset($_GET["feladat"]))
		//echo '<a href = "../szerkeszto/index.php?feladatsor='.$_GET["feladatsor"].'" class = "alap_gomb">Vissza a szerkesztőhöz</a>';
		echo '<a href = "../szerkeszto/index.php?feladatsor='.$_GET["feladatsor"].'&uj=1" class = "alap_gomb">Vissza a szerkesztőhöz</a>';
	$sql = "SELECT * FROM kerdes_feladat WHERE feladatsor_id = '".$_GET["feladatsor"]."'";
	//$sql = "SELECT * FROM ".$_GET["feladatsor"];
	$result = $conn->query($sql);
	$db = 1;
	
	if($result != false && $result->num_rows > 0){
		while($row = $result->fetch_assoc()) {
			$sql = "SELECT * FROM feladatok WHERE id = '".$row["kerdes_id"]."'";
			$result1 = $conn->query($sql);
			$row1 = $result1->fetch_assoc();
			echo "<div class = 'feladat'>";
			echo "<span class = 'feladatSorszama'>".$db.". feladat</span> <a href = '../szerkeszto/loadTaskEdit.php?feladat=".$row['kerdes_id']."&feladatsor=".$_GET["feladatsor"]."' class = 'szerkeszt_gomb inline displayHover'>Szerkesztés</a>";


			echo "<br>".$row1["szoveg"]."<br>";
			echo "<div class='adatok'>";
				echo "<p>Megoldás: ".$row1["megoldas"]."</p>";
				echo "<p>Témakör: ".$row1["temakor"]."</p>";
				echo "<p>Típusa: ".$row1["tipus"]."</p>";
				if($row1["tipus"]==3)
					echo "<p>Válaszok száma: ".$row1["valaszok_szama"]."</p>";
				echo "<p>Pontszám: ".$row["pont"]."</p>";
			echo "</div>";
			echo "</div>";
			$db++;
		}
	}else{
		echo " <p>Nincs adat a táblában!</p>";
	}
	
	
	?>
	</div>

<?php 
	include('../includes/overall/footer.php'); 
	include('../includes/overall/db_disconnect.php');
?>