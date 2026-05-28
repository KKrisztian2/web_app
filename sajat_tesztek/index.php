<?php 
	$oldal = "sajat_tesztek";
	session_start();
	$_SESSION["title"] = "Saját feladatsorok";
	include('../includes/overall/header.php');
	include('../includes/overall/db_connect.php');
	if (isset($_SESSION["rang"]) && $_SESSION["rang"] == "tanar"){ ?>
		<section id = "lehetosegek">
			<p>Új teszt összeállítása:</p>
			<button class = "ujteszt_gomb"><a href = "../sajat_teszt/index.php?sajat=1">Véletlenszerű feladatsor</a></button>
			<button class = "ujteszt_gomb"><a href = "../feladatsor_letrehozasa/index.php">Feladatsor létrehozása</a></button>
			<button class = "ujteszt_gomb"><a href = "#">Feladatok kiválasztása</a></button><br>
			<p>Már elkészített tesztek: </p>
		</section>
	<?php 
	}

	if (isset($_POST["mentes"])){
		$nev = $_POST["nev"];
		megtisztit($_POST["nev"]);
		if ($_SESSION["sajat"] == 1)
			$_POST["nev"] = sajat_nev($_POST["nev"]);
		if (ellenoriz($_POST["nev"]) != ""){
			$_SESSION["hiba"] = ellenoriz($_POST["nev"]);
			//visszairanyitas, hibajelzes
			if ($_SESSION["sajat"] == 1){
				//vissza a generált teszthez
				header ("Location: ../feladatok/diak.php?sajat_teszt=sajat_teszt");
				exit();
			}
			if ($_SESSION["sajat"] == 2){
				header ("Location: ../feladatok/diak.php?feladatsor=".$_SESSION["feladatsor"]);
				exit();
			}
			
		}else {
			//feladatsor mentese
			if ($_SESSION["sajat"] == 2){
				$stmt = $conn->prepare("INSERT INTO feladatsorok (nev,szerkesztette,allapot) VALUES (?, ?, ?)");
				$f = "zart";
				$stmt->bind_param("sis",$_POST["nev"],$_SESSION["felhasznalo"], $f);
				$stmt->execute();
				$stmt->close();
			}else if ($_SESSION["sajat"] == 1){
				$stmt = $conn->prepare("INSERT INTO feladatsorok (nev,szerkesztette,allapot) VALUES (?, ?, ?)");
				$f = "zart";
				$stmt->bind_param("sis",$nev, $_SESSION["felhasznalo"], $f);
				$stmt->execute();
				$fel_sor = $conn->insert_id;
				$stmt->close();

				foreach($_SESSION["beilleszt"] as $sor){
					$stmt = $conn->prepare("INSERT INTO kerdes_feladat (feladatsor_id, kerdes_id, pont) VALUES (?, ?, ?)");
					if($sor["feladat_tipusa"] == 1) $f = 1;
					if($sor["feladat_tipusa"] == 2) $f = 4;
					if($sor["feladat_tipusa"] == 3) $f = 2;
					if($sor["feladat_tipusa"] == 4) $f = 4;
					$stmt->bind_param("iii",$fel_sor, $sor["id"], $f);
					$stmt->execute();
					$stmt->close();
					if ($conn->error){
						echo $conn->error;
					}
				}
				unset($_SESSION["beilleszt"]);
				
			}
		}
	}
		unset($_SESSION["sajat"]);
		unset($_SESSION["hiba"]);
	
?>

<section id = "tesztek">
	<?php
		if (isset($_SESSION["rang"]) && $_SESSION["rang"] == "tanar") {
			// --- Ha épp mentett ---
			if (isset($_POST["kod_mentes"])) {
				$feladatsor_id = intval($_POST["feladatsor"]);
				$kod = trim($_POST["kod"]);

				if ($kod === "") {
					$conn->query("DELETE FROM szoba WHERE feladatsor = $feladatsor_id");
				} else if (!preg_match('/^\d{4}$/', $kod)) {
					echo "<script>alert('A kódnak 4 számjegyből kell állnia!')</script>";
				} else {
					$leker = $conn->query("SELECT * FROM szoba WHERE id = '$kod'");
					if ($leker->num_rows > 0) {
						 echo "<script>alert('Ez a kód már használatban van!');</script>";
					} else {
						$conn->query("DELETE FROM szoba WHERE feladatsor = $feladatsor_id");
						$stmt = $conn->prepare("INSERT INTO szoba (id, feladatsor) VALUES (?, ?)");
						$stmt->bind_param("ii", $kod, $feladatsor_id);
						$stmt->execute();
						$stmt->close();
						echo "<script>alert('A kód sikeresen mentve!');</script>";
					}
				}
			}

			$sql = "SELECT id, nev FROM feladatsorok WHERE szerkesztette = " . $_SESSION["felhasznalo"];
			$result = $conn->query($sql);

			while ($row = $result->fetch_assoc()) {
				echo '<div class="teszt">';
				echo '<h4><a href="../feladatok/feladatok.php?feladatsor=' . $row["id"] . '">' . htmlspecialchars($row["nev"]) . '</a></h4>';

				$kod_sql = "SELECT id FROM szoba WHERE feladatsor = " . $row["id"];
				$kod_result = $conn->query($kod_sql);
				$kod = "";
				if ($kod_result->num_rows > 0) {
					$kod = $kod_result->fetch_assoc()["id"];
				}

				echo '<form method="POST" style="margin-top:10px;">';
				echo '<input type="hidden" name="feladatsor" value="' . $row["id"] . '">';
				echo '<label>Kód:</label> ';
				echo '<input type="text" name="kod" maxlength="4" value="' . htmlspecialchars($kod) . '" style="width:60px;text-align:center;">';
				echo ' <button type="submit" name="kod_mentes" class="alap_gomb">Mentés</button>';
				echo '</form>';

				echo '<ul class="megjeleno">';
				$sql = "SELECT csoport_id FROM kiadott_feladatsorok WHERE feladatsor_id=" . $row["id"];
				$csoportok = $conn->query($sql);
				while ($nev = $csoportok->fetch_assoc()) {
					$sql = "SELECT nev FROM csoportok WHERE id=" . $nev["csoport_id"];
					$result1 = $conn->query($sql);
					$nev = $result1->fetch_assoc();
					echo '<li>' . htmlspecialchars($nev["nev"]) . '</li>';
				}
				echo '</ul>';

				echo '<button class="alap_gomb kiad_gomb">Kiad</button>';
				echo '</div>';
			}
		}
		else if(isset($_SESSION["rang"]) && $_SESSION["rang"] == "diak"){
			$sql="SELECT csoport_id FROM csoporttagok WHERE felhasznalo_id=".$_SESSION["felhasznalo"];
			$result = $conn->query($sql);
			while ($csoportok = $result->fetch_assoc()) {
				$sql="SELECT feladatsor_id FROM kiadott_feladatsorok WHERE csoport_id=".$csoportok["csoport_id"];
				$result2 = $conn->query($sql);
				while ($feladatsor = $result2->fetch_assoc()){
					$sql="SELECT nev FROM feladatsorok WHERE id=".$feladatsor["feladatsor_id"];
					$result3 = $conn->query($sql);
					$nev = $result3->fetch_assoc();
					echo '<div class = "teszt">';
					echo '<h4><a href = "../feladatok/diak.php?feladatsor='.$feladatsor["feladatsor_id"].'">'.$nev["nev"].'</a></h4>';
					echo '</div>';
				}
			}
		}
		?>
		<div id="ki">
			<span id = "bezar_gomb"><i class="fa fa-close"></i></span>
			<div id = "flex">
				<div id="nevsor">
					<label for = "kereses">Keresendő csoport:</label><br>
					<input type = "text" name = "kereses" id = "kereses">
						<ul>
						<?php
							$sql="SELECT nev FROM csoportok";
							$result=$conn->query($sql);
							while($nev=$result->fetch_assoc()){
								echo '<li>'.$nev["nev"].'</li>';
							}
						?>
						</ul>
				</div>
				<div id="gombok">
					<button id="bedob"><i class="fa fa-arrow-right"></i></button><br>
					<button id="kidob"><i class="fa fa-arrow-left"></i></button>
				</div>
				<div id="csoport">
					<ul>
					</ul>
				</div>
				<div>
					<button class="alap_gomb" id="kiad">Kiad</button>
				</div>
				</form>
			</div>
		</div>
		
</section>
<section id = "uzenet">
	
</section>
<?php 
	function megtisztit($adat){
		$adat = trim($adat);
		$adat=stripslashes($adat);
		$adat=htmlspecialchars($adat);
		for ($i = 0; $i < strlen($adat); $i++){
			if (substr($adat, $i, 1) == " "){
				$adat[$i] = '_';
			}
		}
		return $adat;
	}
	function ellenoriz($adat){
		global $conn;
		$hiba = "";
		if ($_SESSION["sajat"] == 1){
			$result = $conn->query("SELECT * FROM feladatsorok WHERE nev = '".$adat."'");
				if ($result->num_rows > 0){
					$hiba = "mar letezik";
				}
				return $hiba;
		}
	}
	
	function sajat_nev($adat){
		$adat = "sajat_".$_SESSION["felhasznalo"]."_".$_POST["nev"];
		return $adat;
	}
	include('../includes/overall/footer.php'); 
	include('../includes/overall/db_disconnect.php');
?>