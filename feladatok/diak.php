<?php
	session_start();
	$oldal = "szerkesztodiak";
	$_SESSION["title"] = "Gyakorló teszt";
	include('../includes/overall/header.php');
	include('../includes/overall/db_connect.php');
?>
		
<div class="feladatok">
<?php
	$db = 1;
	$adatok = [];
	$ures = 0;
	$nyilvanos = 1;

	if (isset($_SESSION["sajat"]) && $_SESSION["rang"] == "tanar"){
		$nyilvanos = 2;
		echo "<form action='../sajat_tesztek/index.php' method='post'>";
		if (isset($_SESSION["hiba"]) && $_SESSION["hiba"] == "mar letezik"){
			echo "<p style='color: red'>Ilyen néven már létezik feladatsor!</p>";
		}
		if (isset($_GET["feladatsor"]))
			$_SESSION["feladatsor"] = $_GET["feladatsor"];
		
		echo "<input type='text' placeholder='A feladatsor neve' id='nev' name='nev' required";
		if (isset($_POST["nev"])) echo " value='".$_POST["nev"]."'>";
		else echo ">";
		echo "<input type='submit' name='mentes' id='kiad_gomb' value='Mentés'>";
		echo "</form>";
	}

	if(isset($_GET["sajat_teszt"]) && isset($_SESSION["sajat_teszt"])){
		$db = count($_SESSION["sajat_teszt"]);
		feladatokLetrehozasaGombokKiirasa();
	}else if(isset($_GET["sajat_teszt"]) && isset($_POST["szam"])){
		$szintKriterium = "";
		$szintek = getTipusok();
		$temakor_tablak = getTemakorTablak($szintek);
		$feladatok_szama = $_POST["szam"];	
		$_SESSION["sajat_teszt"] = ["szintKriterium"=>$szintKriterium];

		$a = 0;
		foreach($temakor_tablak as $tabla){
			$a += count($tabla["feladatok"]);
		}
		if($a < $feladatok_szama) $feladatok_szama = $a;

		$temakorok_szama = count($temakor_tablak);
		$seged = [];
		while($db <= $feladatok_szama){
			$tabla_sorszam = rand(0,$temakorok_szama-1);
			$sor = rand(1,count($temakor_tablak[$tabla_sorszam]["feladatok"]));
			if(!in_array($tabla_sorszam."|".$sor,$seged)){
				$_SESSION["sajat_teszt"][$db-1]["tabla"] = $temakor_tablak[$tabla_sorszam]["tabla"];
				$_SESSION["sajat_teszt"][$db-1]["feladat"] = $temakor_tablak[$tabla_sorszam]["feladatok"][$sor-1];
				$seged[$db-1] = $tabla_sorszam."|".$sor;
				$db++;
			}
		}
		feladatokLetrehozasaGombokKiirasa();
	}else if(!isset($_GET["sajat_teszt"])){
		$sql = "SELECT kerdes_id FROM kerdes_feladat WHERE feladatsor_id='".$_GET["feladatsor"]."'";
		$result = $conn->query($sql);
		if($result != false && $result->num_rows > 0){
			echo "<div class='gombok'>";
			while($row = $result->fetch_assoc()) {
				$sql = "SELECT * FROM feladatok WHERE id='".$row["kerdes_id"]."'";
				$result1 = $conn->query($sql);
				$row1 = $result1->fetch_assoc();
				$adatok["feladat"][$db-1] = $row1["szoveg"];
				$adatok["id"][$db-1] = $row1["id"];
				$adatok["feladat_tipusa"][$db-1] = $row1["tipus"];
				$adatok["valaszok_szama"][$db-1] = $row1["valaszok_szama"];
				$adatok["megoldas"][$db-1] = $row1["megoldas"];
				if ($db == 1)
					echo '<button class="sorszam akt" onclick="feladatvaltas(this)">'.$db.'</button>';
				else 
					echo '<button class="sorszam" onclick="feladatvaltas(this)">'.$db.'</button>';
				$db++;
			}
			echo "</div>";
		}else{
			$ures = 1;
			echo "Nincs adat a táblában!";
		}
	}

	echo "<form method='post' action=''>";
	echo "<div id='feladatok'>";

	$maxpont = 0;
	$eredmeny = 0;

	for($i=0;$i<$db-1;$i++){
		$feladat_div_class = ($i==0)?"feladat aktiv":"feladat";
		$feladat_div_style = ($i==0)?"display: block":"display: none";
		echo "<div class='$feladat_div_class' id='$i' style='$feladat_div_style'>";
		echo "<br>".$adatok["feladat"][$i]."<br>";
		echo "</div>";

		$megoldas_div_style = ($i!=0)?"style='display: none'":"";
		echo "<div class='megoldas' $megoldas_div_style>";

		// --- Pont számítása ---
		$pont = 0;
		if(isset($_GET["sajat_teszt"]) && isset($_SESSION["sajat_teszt"])){
			if($adatok["feladat_tipusa"][$i]==1) $pont=1;
			else if($adatok["feladat_tipusa"][$i]==2) $pont=4;
			else if($adatok["feladat_tipusa"][$i]==3) $pont=2*$adatok["valaszok_szama"][$i];
		}else if(isset($_GET["feladatsor"])){
			$feladatsor_id=intval($_GET["feladatsor"]);
			$feladat_id=intval($adatok["id"][$i]);
			$sql="SELECT pont FROM kerdes_feladat WHERE feladatsor_id=$feladatsor_id AND kerdes_id=$feladat_id";
			$result=$conn->query($sql);
			if($result && $result->num_rows>0){
				$row=$result->fetch_assoc();
				$pont=intval($row["pont"]);
			}else{
				if($adatok["feladat_tipusa"][$i]==1) $pont=1;
				else if($adatok["feladat_tipusa"][$i]==2) $pont=4;
				else if($adatok["feladat_tipusa"][$i]==3) $pont=2*$adatok["valaszok_szama"][$i];
			}
		}

		$maxpont+=$pont;

		// --- Feladat típusok kezelése ---
		if($adatok["feladat_tipusa"][$i]==1){
			echo "<p>Az ön válasza:</p>";
			echo "<table><tr><td>A</td><td>B</td><td>C</td><td>D</td><td>E</td></tr><tr>";
			foreach(["A","B","C","D","E"] as $v){
				echo "<td><input type='radio' name='$i' value='$v'";
				if(isset($_POST[$i]) && $_POST[$i]==$v) echo " checked";
				else if(isset($_POST["submit"])) echo " disabled";
				echo "></td>";
			}
			if(isset($_POST["submit"])){
				if(isset($_POST[$i]) && $_POST[$i]==$adatok["megoldas"][$i]){
					$eredmeny+=$pont;
					echo "<td><i class='fa fa-check' style='font-size:20px;color:green'></i></td>";
				}
				else if (isset($_POST["submit"])) {
					echo "<td><i class='fa fa-times' style='font-size:20px;color:red'></i></td>";
				}
			}
			echo "</tr></table>";

			if(isset($_POST["submit"])){
				echo "<p>A helyes válasz:</p>";
				foreach(["A","B","C","D","E"] as $v){
					echo "<input type='radio' value='$v'";
					if($adatok["megoldas"][$i]==$v) echo " checked";
					else echo " disabled";
					echo ">";
				}
			}

		}else if($adatok["feladat_tipusa"][$i]==2){
			echo "<p>Az ön válasza:</p>";
			echo "<table><tr><th></th><th>A</th><th>B</th><th>C</th><th>D</th><th>E</th></tr>";
			$res_pont=$pont/4;
			$nevek=["_elso","_masodik","_harmadik","_negyedik"];
			for($k=0;$k<4;$k++){
				echo "<tr><th>".($k+1)."</th>";
				foreach(["A","B","C","D","E"] as $v){
					$name=$i.$nevek[$k];
					echo "<td><input type='radio' name='$name' value='$v'";
					if(isset($_POST[$name]) && $_POST[$name]==$v) echo " checked";
					else if(isset($_POST["submit"])) echo " disabled";
					echo "></td>";
				}
				if(isset($_POST["submit"])){
					$name = $i.$nevek[$k];
					if(isset($_POST[$name]) && $_POST[$name] == substr($adatok["megoldas"][$i], $k*2, 1)) {
						$eredmeny += $res_pont;
						echo "<td><i class='fa fa-check' style='font-size:20px;color:green'></i></td>";
					} else {
						echo "<td><i class='fa fa-times' style='font-size:20px;color:red'></i></td>";
					}
				}
				echo "</tr>";
			}
			echo "</table>";

			if(isset($_POST["submit"])){
				echo "<p>A helyes válasz:</p>";
				echo "<table><tr><th></th><th>A</th><th>B</th><th>C</th><th>D</th><th>E</th></tr>";
				for($k=0;$k<4;$k++){
					echo "<tr><th>".($k+1)."</th>";
					foreach(["A","B","C","D","E"] as $v){
						echo "<td><input type='radio' value='$v'";
						if(substr($adatok["megoldas"][$i],$k*2,1)==$v) echo " checked";
						else echo " disabled";
						echo "></td>";
					}
					echo "</tr>";
				}
				echo "</table>";
			}

		}else if($adatok["feladat_tipusa"][$i]==3){
			$res_pont=$pont/$adatok["valaszok_szama"][$i];
			$answers=explode("\t",$adatok["megoldas"][$i]);
			for($k=1;$k<=$adatok["valaszok_szama"][$i];$k++){
				$name=$i."_".$k;
				echo "<p>Az ön válasza:</p>";
				echo "<input type='text' name='$name'";
				if(isset($_POST[$name])) echo " value='".$_POST[$name]."' readonly";
				echo ">";
				if(isset($_POST["submit"])){
					$helyes=isset($answers[$k-1])?$answers[$k-1]:"";
					if(isset($_POST[$name]) && $_POST[$name]==$helyes) $eredmeny+=$res_pont;
					echo "<i class='fa fa-".((isset($_POST[$name]) && $_POST[$name]==$helyes)?"check' style='font-size:20px;color:green":"times' style='font-size:20px;color:red")."'></i>";
					echo "<p>A helyes válasz:</p>";
					echo "<input type='text' value='$helyes' readonly>";
				}
			}
		}

		echo "</div>";
	}

	echo "</div>";

	if (isset($_POST["submit"]) && !isset($_GET["sajat_teszt"]) && $ures != 1) {
		$ma = date("Y-m-d H:i:s");
		$szazalek = round($eredmeny / $maxpont * 100, 2);
		$feladatsor_id = intval($_GET["feladatsor"]);

		if (isset($_SESSION["felhasznalo"])) {
			$diak_id = intval($_SESSION["felhasznalo"]);
		} elseif (isset($_SESSION["azonosito"])) {
			$diak_id = "'" . $conn->real_escape_string($_SESSION["azonosito"]) . "'";
		} else {
			$diak_id = "'ismeretlen'";
		}

		$sql = "INSERT INTO eredmenyek (diak_id, feladatsor_id, pontszam, szazalek, datum)
				VALUES ($diak_id, $feladatsor_id, $eredmeny, $szazalek, '$ma')";
		$result = $conn->query($sql);
	}

	// Gombok
	echo "<div class='gombok'>";
	echo "<button type='button' onclick='elozo()' class='alap_gomb_kek'><< Vissza</button>";
	if(!isset($_POST["submit"]) && !isset($_SESSION["sajat"])){
		echo "<input type='submit' name='submit' value='Befejezés' id='submit' onclick='befejezes()' class='alap_gomb_piros'>";
	}
	echo "<button type='button' onclick='kovetkezo()' class='alap_gomb_kek'>Tovább >></button>";
	echo "</div>";

	echo "</form>";
?>
<div id="eredmeny" <?php if(isset($_POST["submit"])) echo "style='display: block'"; ?>>
	<p>Az ön eredménye: </p>
	<span>
	<?php
		if(isset($eredmeny) && $ures!=1){
			echo round($eredmeny/$maxpont*100,2)." %";
		}else if($ures==1){
			echo "0";
		}
	?>
	</span><br>
	<button id="oke">Oké <i class="fa fa-check"></i></button>
</div>

<script src='https://kit.fontawesome.com/a076d05399.js'></script>
<?php 
	include('../includes/overall/footer.php'); 
	include('../includes/overall/db_disconnect.php');
?>

<?php
	function valtozoKiir($valtozo){
		echo "<pre>";
		print_r($valtozo);
		echo "</pre>";
	}
	
	function getTemakorTablak($szintek) {
    global $conn;

    $tipusKriterium = [];
    for ($i = 0; $i < 4; $i++) {
        if ($szintek[$i]) $tipusKriterium[] = "tipus=" . ($i + 1);
    }
    $tipusSQL = count($tipusKriterium) > 0 ? "(" . implode(" OR ", $tipusKriterium) . ")" : "1";

    $temakor_tablak = [];
    $temakorok_szama = 0;
	global $nyilvanos;

    if (isset($_POST["osszes_temakor"])) {
        $sql = "SELECT DISTINCT temakor FROM feladatok WHERE nyilvanos='".$nyilvanos."' AND $tipusSQL";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $sql2 = "SELECT * FROM feladatok WHERE temakor='".$conn->real_escape_string($row["temakor"])."' AND nyilvanos='".$nyilvanos."' AND $tipusSQL";
            $res2 = $conn->query($sql2);
            if ($res2 && $res2->num_rows > 0) {
                $temakor_tablak[$temakorok_szama]["tabla"] = "feladatok";
                $temakor_tablak[$temakorok_szama]["temakor"] = $row["temakor"];
                $temakor_tablak[$temakorok_szama]["feladatok"] = $res2->fetch_all(MYSQLI_ASSOC);
                $temakorok_szama++;
            }
        }
    } else {
        $sql = "SELECT DISTINCT temakor FROM feladatok";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $safeTemakor = preg_replace('/[^a-zA-Z0-9_-]/u', '_', $row["temakor"]);
            $kulcs = "temakor_" . $safeTemakor;

            if (array_key_exists($kulcs, $_POST)) {
                $sql2 = "SELECT * FROM feladatok WHERE temakor='".$conn->real_escape_string($row["temakor"])."' AND nyilvanos='".$nyilvanos."' AND $tipusSQL";
                $res2 = $conn->query($sql2);
                if ($res2 && $res2->num_rows > 0) {
                    $temakor_tablak[$temakorok_szama]["tabla"] = "feladatok";
                    $temakor_tablak[$temakorok_szama]["temakor"] = $row["temakor"];
                    $temakor_tablak[$temakorok_szama]["feladatok"] = $res2->fetch_all(MYSQLI_ASSOC);
                    $temakorok_szama++;
                }
            }
        }
    }

    return $temakor_tablak;
}


	function getTipusok(){
		$tipusok=[false,false,false,false];
		if(isset($_POST["osszes_tipus"])) $tipusok=[true,true,true,true];
		else{
			for($i=1;$i<=4;$i++){
				$kulcs="tipus_".$i;
				if(array_key_exists($kulcs,$_POST)) $tipusok[$i-1]=true;
			}
		}
		return $tipusok;
	}

	function feladatokLetrehozasaGombokKiirasa(){
		global $conn,$adatok,$db, $nyilvanos;
		echo "<div class='gombok'>";
		for($i=0;$i<$db-1;$i++){
			$sql="SELECT * FROM feladatok WHERE id=".$_SESSION["sajat_teszt"][$i]["feladat"]["id"]." AND nyilvanos='".$nyilvanos."'";
			$result=$conn->query($sql);
			if(!$result || $result->num_rows==0) continue;
			$row=$result->fetch_assoc();
			$adatok["id"][$i]=$row["id"];
			$adatok["feladat_tipusa"][$i]=$row["tipus"];
			$adatok["feladat"][$i]=$row["szoveg"];
			$adatok["id"][$i]=$row["id"];
			$adatok["feladat_tipusa"][$i]=$row["tipus"];
			$adatok["valaszok_szama"][$i]=$row["valaszok_szama"];
			$adatok["megoldas"][$i]=$row["megoldas"];
			$adatok["temakor"][$i]=$row["temakor"];
			if(isset($_SESSION["sajat"]) && $_SESSION["sajat"]==1){
				$_SESSION["beilleszt"][$i]["id"]=$i+1;
				$_SESSION["beilleszt"][$i]["feladat_tipusa"]=$adatok["feladat_tipusa"][$i];
			}
			if($i==0) echo '<button class="sorszam akt" onclick="feladatvaltas(this)">'.($i+1).'</button>';
			else echo '<button class="sorszam" onclick="feladatvaltas(this)">'.($i+1).'</button>';
		}
		echo "</div>";
	}
?>
