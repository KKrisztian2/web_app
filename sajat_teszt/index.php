<?php 
	$oldal = "sajatteszt";
	session_start();
	$_SESSION["title"] = "Generált teszt";
	include('../includes/overall/header.php');
	include('../includes/overall/db_connect.php');
	$tomb = [];
	$db = 0;
	$sql = "SELECT DISTINCT temakor FROM feladatok ORDER BY temakor";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc()){
		$tomb[$db] = $row["temakor"];
		$db++;
	}
	if (isset($_GET["sajat"]) && $_SESSION["rang"] = "tanar"){
		$_SESSION["sajat"] = $_GET["sajat"];
 	}
	else {
			if (isset($_SESSION["sajat"]));
				unset($_SESSION["sajat"]);
		}
?>
			<h3>Saját teszt létrehozása</h3>
			<p>Generált teszt indításához töltse ki az alábbi űrlapot: </p>
				
			<form method = "post" action = "../feladatok/diak.php?sajat_teszt=sajat_teszt" onsubmit = "return feladatSzamaEllenorzes()">
				<div id = "elso">
					<div id="kereso-wrapper">
						<input type="text" id="keres-temakor" placeholder="Témakör keresése...">
					</div>
					<label for = "checkbox">Összes témakör</label>
					<input type = "checkbox" name = "osszes_temakor" id = "osszes_temakor" ><br>

					<div id="temakor_lista">
					<?php 
						for($i=0;$i<$db; $i++){
							$sum = $i+1;
							echo "<div class='temakor_elem'>";
							echo "<label for = 'temakor_".$sum."'>".$tomb[$i]."</label>";
							/*echo "<input type = 'checkbox' name = 'temakor' id ='$tomb[$i]' value = '".$tomb[$i]."' > <br>";*/
							$safeTemakor = preg_replace('/[^a-zA-Z0-9_-]/u', '_', $tomb[$i]);
							echo "<input type='checkbox' name='temakor_$safeTemakor' id='$safeTemakor' value='".$tomb[$i]."'><br>";
							echo "</div>";
						}	
					?>
					</div>
					<button type = "button" class = "alap_gomb" id = "temakor_tovabb">Tovább</button>
				</div>
				
				<div id = "masodik">
					<label for = "szint">Összes szint</label>
					<input type = "checkbox" name = "osszes_szint" id = "osszes_szint" ><br>
					<label for = "szint_1">I. szint</label>
					<input type = "checkbox" name = "szint_1" id = "1" value = "1" ><br>
					<label for = "szint_2">II. szint</label>
					<input type = "checkbox" name = "szint_2" id = "2" value = "2" ><br>
					<label for = "szint_3">III. szint</label>
					<input type = "checkbox" name = "szint_3" id = "3" value = "3" ><br>
					<label for = "szint_4">IV. szint</label>
					<input type = "checkbox" name = "szint_4" id = "4" value = "4" ><br>
					<button type = "button" class = "alap_gomb" id = "szint_tovabb">Tovább</button>
				</div>
				
				<div id = "harmadik">
					<label for = "szam">Hány feladatot szeretne?</label><br>
					<input type = "number" name = "szam" id = "szam" min = "1"  value = "1">
					<input type = "submit" name = "inditas" class = "alap_gomb" value = "Teszt indítása">
				</div>
			</form>

			<script>
			document.addEventListener("DOMContentLoaded", function() {
				const keresInput = document.getElementById("keres-temakor");
				const temakorok = document.querySelectorAll("#temakor_lista .temakor_elem");

				if (keresInput) {
					keresInput.addEventListener("keyup", function() {
						const filter = keresInput.value.toLowerCase();

						temakorok.forEach(div => {
							const label = div.querySelector("label")?.textContent.toLowerCase() || "";
							div.style.display = label.includes(filter) ? "block" : "none";
						});
					});
				}
			});
			</script>
			
			<div id = "warning">
				<div class = "dialog_header">
					Figyelem!
					<span id = "close_dialog">+</span>
				</div>
				<div class = "dialog_body">
					<p id = "warning_text"></p>
				</div>
			</div>
<?php 
	include('../includes/overall/footer.php'); 
	include('../includes/overall/db_disconnect.php');
?>