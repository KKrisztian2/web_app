<?php 
	$oldal = "feladatsorletrehoz";
	session_start();
	$_SESSION["title"] = "Feladatsorok kezelése";
	include('../includes/overall/header.php');
	include('../includes/overall/db_connect.php');
?>

<?php
	if(isset($_SESSION["rang"]) && $_SESSION["rang"] != "tanar") header('Location: ../index.php');
	if(isset($_POST["kuldes"]) && $_POST["nev"] != ""){
		$sql="SELECT nev FROM feladatsorok WHERE nev = '".$_POST["nev"]."'";
		$result=$conn->query($sql);
		$hiba="";
		if($result->num_rows>0) $hiba="Ez a feladatsor név már létezik!";
		if($hiba==""){
			$sql = "INSERT INTO feladatsorok (nev, szerkesztette, allapot) VALUES ('".$_POST["nev"]."' , '".$_SESSION["felhasznalo"]."' , 'zart')";
			$result=$conn->query($sql);
			$sql = "SELECT id FROM feladatsorok WHERE nev = '".$_POST["nev"]."'";
			$result=$conn->query($sql);
			$nev=$result->fetch_array();
			include('../includes/overall/db_disconnect.php');
			header('Location: ../szerkeszto/index.php?feladatsor='.$nev["id"].'&uj=1');
			exit();
		}
		else{
			echo "<div id = 'hibajelzes'>
					<p>$hiba</p>
				</div>";
		}
	}
	$hiba="";
	echo "<div id = 'hibajelzes'>
		<p>$hiba</p>
		</div>";

?>

	<p>Alább találja az ön által szerkesztett feladatsorokat. Lehetősége van ezeket szerkeszteni, nyilvánossá/zártá tenni, törölni. <br> Illetve itt tud feltölteni új feladatot az adatbázisba. <br> 
		<button class = "pelda_gomb"><i class = 'fa fa-edit'></i></button> - feladatsor szerkesztése <br>
		<button class = "pelda_gomb"><i class = 'fa fa-upload'></i></button> - feladatsor feltöltése <br>
		<button class = "pelda_gomb"><i class = 'fa fa-trash'></i></button> - feladatsor végleges törlése <br>
		<button class = "pelda_gomb"><i class = 'fa fa-download'></i></button> - feladatsor feltöltésének visszavonása
	</p>
	<button id = "alap_gomb">Új feladatsor feltöltése</button><br>
	<?php
		echo "<ul id = 'kesz_feladatsorok'>";

		$sql="SELECT * FROM feladatsorok WHERE szerkesztette='".$_SESSION["felhasznalo"]."' ORDER BY nev DESC" ;
		$result=$conn->query($sql);
		for($i=0;$i<$result->num_rows;$i++){
			echo "<li>";
			echo "<ul>";
			$row=$result->fetch_array();
					echo "<li>";
					echo "<p class='nev'>";
					echo $row["nev"];
					echo "</p>";
						echo "<p>".$row["allapot"]."</p>";
						echo "<div class = 'gombok_listaja'>";
							if ($row["allapot"] != "nyilvanos")
								echo "<a class = 'szerk' href = '../feladatok/feladatok.php?feladatsor=".$row["id"]."'><i class = 'fa fa-edit'></i></a>";
							if ($row["allapot"] == "zart")
								echo "<a class = 'kozzetetel' href = 'feltoltes.php?feladatsor=".$row["id"]."'><i class = 'fa fa-upload'></i></a>";
							else if($row["allapot"] == "nyilvanos")
								echo "<a class = 'visszavonas' href = 'visszavonas.php?feladatsor=".$row["id"]."'><i class = 'fa fa-download'></i></a>";
							echo "<a class = 'torles' href = 'torles.php?feladatsor=".$row["id"]."'><i class = 'fa fa-trash'></i></a>";
						echo "</div>";
					echo "</li>";
			echo "</ul>";		
			echo "</li>";
		}
		echo "</ul>";
	?>
	
	<div id = "ujznoteszt_div">
		<form method = "post" action = "">
			<span id = "bezar_gomb"><i class="fa fa-close"></i></span>
			<label for = "nev">Adja meg a feladatsor nevét</label><br>
			<input type="text" name="nev" id="nev"><br>
			<input type = "submit" value = "Küldés" id = "alap_gomb" name = "kuldes">
		</form>
	</div>
	<div id="torles_main">
		<div id="torles_uzenet">
			<span id = "torles_uzenet_bezar"><i class="fa fa-close"></i></span>
		</div>
	</div>
<?php 
	include('../includes/overall/footer.php'); 
	include('../includes/overall/db_disconnect.php');
?>