<?php 
	session_start();
	$_SESSION["title"] = "Nyilvános teszt";
	$oldal = "nyilvanostesztek";
	include('../includes/overall/header.php');
	include('../includes/overall/db_connect.php');
?>

<div class="leiras">
	<p>Alább találja a mindenki számára nyilvános feladatsorokat, így gyakorolhat adott témakörökben.</p>
	<p>A teszt indításához csak kattintson a kívánt feladatsorra:</p>
</div>

<?php
	if (isset($_GET["sajat"]))
		$_SESSION["sajat"] = $_GET["sajat"];
	else {
		if (isset($_SESSION["sajat"]))
			unset($_SESSION["sajat"]);
	}

	$sql = "SELECT * FROM feladatsorok WHERE allapot='nyilvanos' ORDER BY nev ASC";
	$result = $conn->query($sql);
?>

<div id="kesz_feladatsorok">
	<?php while($row = $result->fetch_assoc()): ?>
		<div class="feladatsor-card">
			<a href="../feladatok/diak.php?feladatsor=<?php echo $row['id']; ?>&gyak=1">
				<?php echo htmlspecialchars($row['nev']); ?>
			</a>
		</div>
	<?php endwhile; ?>
</div>

<div class="leiras">
	<p>A gyakorlást elősegítendő véletlenszerű feladatsor generálására is van lehetősége (akár adott témakörök kiválasztásával), ehhez válassza a 2. menüpontot.</p>
</div>

<?php 
	include('../includes/overall/footer.php');
	include('../includes/overall/db_disconnect.php');
?>