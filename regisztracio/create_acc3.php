<?php
	session_start();
	$_SESSION["title"] = "Regisztráció";
	$oldal = "login";
	include('../includes/overall/header.php');

	$rang = "";
	$hibak = [];

	if (isset($_SESSION["rang"]) && $_SESSION["rang"] == "tanar" && isset($_SESSION["new_email"])) {
		if (isset($_POST["tovabb"])) {
			$rang = megtisztit($_POST["rang"]);

			if ($rang != "diak" && $rang != "tanar") {
				$hibak["rang"] = "Érvénytelen rang.";
			}

			if (count($hibak) == 0) {
				$_SESSION["new_rang"] = $rang;
				header("Location: create_acc4.php");
				exit();
			}
		}
	} else {
		header("Location: create_acc2.php");
		exit();
	}

	function megtisztit($adat) {
		$adat = trim($adat);
		$adat = stripslashes($adat);
		$adat = htmlspecialchars($adat);
		return $adat;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Regisztráció</title>
		<meta charset="utf-8">
	</head>
	<body>
		<div id="doboz">
			<?php
				if (isset($hibak["rang"])) {
					echo "<div id='hiba'>";
					echo $hibak["rang"];
					echo '<button id="bezar" onclick="bezar()">x</button>';
					echo "</div>";
				}
			?>
		</div>

		<form action="" method="post">
			<div id="fejlec">
				<h2>Regisztráció</h2>
				<h3>Regisztráljon egy új fiókot</h3>
			</div>

			<select name="rang" id="rang">
				<option value="diak" <?php if($rang=="diak") echo "selected"; ?>>Diák</option>
				<option value="tanar" <?php if($rang=="tanar") echo "selected"; ?>>Tanár</option>
			</select><br>

			<input type="submit" name="tovabb" value="Tovább">
		</form>
	</body>
</html>

<?php
	include('../includes/overall/footer.php');
?>
