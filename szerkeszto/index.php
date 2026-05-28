<?php
session_start();
if(isset($_SESSION["error"])){
    if($_SESSION["error"] == false)
        $message = $_SESSION["mesage"];
    else
        $error_message = $_SESSION["error_message"];
    unset($_SESSION["error"]);
}

if (isset($_GET['uj']) && $_GET['uj'] == 1) {
    unset($_SESSION["edit_task"]);
    unset($_SESSION["edit_task_id"]);
}

$oldal = "szerkeszto";
$_SESSION["title"] = "Szerkesztő";
include('../includes/overall/header.php');
include('../includes/overall/db_connect.php');
?>

<main>
    <div style="display: flex; flex-wrap: wrap;">
        <a href="../feladatok/feladatok.php?feladatsor=<?php echo $_GET["feladatsor"]?>" class="alap_gomb">Feladatok</a>
        <span class="error general" id="generalError"><?php if(isset($error_message)) echo $error_message; ?></span>
        <span class="registOK" id="registOK"><?php if(isset($message)) echo $message?></span>
    </div>

    <form action="save.php?feladatsor=<?php echo $_GET["feladatsor"] ?>" method="post">
        <textarea name="szoveg" rows="22"><?php 
            if(isset($_SESSION["edit_task"])){ 
                echo $_SESSION["edit_task"]["szoveg"];
            }
        ?></textarea>
        <br>
        <div id="plusz_informaciok">
            <label for="nyilvanos">Nyilvanos:</label>
            <?php
                $nyilv = 1;
                if(isset($_SESSION["edit_task_id"])){
                    $sql = "SELECT nyilvanos FROM feladatok WHERE id = ".$_SESSION["edit_task_id"];
                    $result = $conn->query($sql);
                    $nyilvanos = $result->fetch_assoc();
                    $nyilv = $nyilvanos['nyilvanos'];
                }
            ?>
            <select name="nyilvanos" id="nyilvanos">
                <option value="1" <?php if($nyilv == 1) echo "selected"; ?>>Nyilvános</option>
                <option value="2" <?php if($nyilv == 2) echo "selected"; ?>>Rejtett</option>
            </select>

            <label for="tipus">Feladat típusa:</label>
            <?php
                $tipus_val = 1; 
                if(isset($_SESSION["edit_task_id"])){
                    $sql = "SELECT tipus FROM feladatok WHERE id = ".$_SESSION["edit_task_id"];
                    $result = $conn->query($sql);
                    $tipus = $result->fetch_assoc();
                    $tipus_val = $tipus["tipus"];
                }
                $tipusok = [1=>"Feleletválasztós",2=>"Logikai párok",3=>"Rövid válasz",4=>"Kidolgozós"];
            ?>
            <select name="tipus" id="tipus">
                <?php
                    foreach($tipusok as $id => $nev){
                        $sel = ($id == $tipus_val) ? "selected" : "";
                        echo "<option value='$id' $sel>$nev</option>";
                    }
                ?>
            </select>

            <label for="temakor">Feladat témaköre:</label>
            <?php
                $temakor_val = "";
                if(isset($_SESSION["edit_task_id"])){
                    $sql = "SELECT temakor FROM feladatok WHERE id = ".$_SESSION["edit_task_id"];
                    $result = $conn->query($sql);
                    $temakor = $result->fetch_assoc();
                    $temakor_val = $temakor["temakor"];
                }
            ?>
            <input type="text" name="temakor" id="temakor" value="<?php echo $temakor_val; ?>">

			<label for="pont">Pontszám:</label>
			<?php
			$pont_val = 1;
                if(isset($_SESSION["edit_task_id"])){
                    $sql = "SELECT pont FROM kerdes_feladat WHERE kerdes_id = ".$_SESSION["edit_task_id"];
                    $result = $conn->query($sql);
                    $pont = $result->fetch_assoc();
                    $pont_val = $pont["pont"];
                }
			?>
			<input type="number" name="pont" id="pont" min="1" value="<?php echo $pont_val; ?>">

            <?php
                $megoldas = ["","",""];
                if(isset($_SESSION["edit_task"]["megoldas"])){
                    switch($_SESSION["edit_task"]["tipus"]){
                        case 1: 
                            $megoldas[0] = $_SESSION["edit_task"]["megoldas"];
                            break;
                        case 2: 
                        case 3: 
                            $megoldas = explode("\t",$_SESSION["edit_task"]["megoldas"]);
                            break;
                    }
                }
            ?>
            <table>
                <?php
                    $lista1 = ["1","2","3","4"];
                    $lista2 = ["A","B","C","D","E"];
                    $nevek = ["elso","masodik","harmadik","negyedik"];
                    echo "<tr><th></th>";
                    foreach($lista2 as $ertek) echo "<th>".$ertek."</th>";
                    echo "</tr>";
                    foreach($lista1 as $index => $ertek1){
                        echo "<tr><th>".$ertek1."</th>";
                        foreach($lista2 as $ertek2){
                            if(isset($_SESSION["edit_task_id"]) && $tipus_val == 2 && $ertek2 == $megoldas[$index]){
                                echo "<td><input type='radio' value='".$ertek2."' name='".$nevek[$index]."' checked></td>";
                            } else {
                                echo "<td><input type='radio' value='".$ertek2."' name='".$nevek[$index]."'></td>";
                            }
                        }
                        echo "</tr>";
                    }
                ?>
            </table>

            <label for="db">Válaszok száma:</label>
            <select name="db" id="db">
                <option value="">Válasszon</option>
                <?php
                    $valaszok_szama = 3;
                    for($i = 1; $i <= $valaszok_szama; $i++){
                        $selected = (isset($_SESSION["edit_task"]["valaszok_szama"]) && $_SESSION["edit_task"]["valaszok_szama"] == $i) ? "selected" : "";
                        echo "<option value='".$i."' $selected>".$i."</option>";
                    }
                ?>
            </select>

            <label for="helyes_teszt">Helyes válasz:</label>
            <select name="helyes_teszt" id="helyes_teszt">
                <option value="">Válasszon</option>
                <?php
                    $valaszok = ["A","B","C","D","E"];
                    for($i=0;$i<5;$i++){
                        $selected = (isset($_SESSION["edit_task"]["megoldas"]) && $_SESSION["edit_task"]["megoldas"]==$valaszok[$i]) ? "selected" : "";
                        echo "<option value='".$valaszok[$i]."' $selected>".$valaszok[$i]."</option>";
                    }
                ?>
            </select>

            <label for="helyes1">Helyes válaszok:</label>
            <input type="text" name="helyes1" id="helyes1" value="<?php echo $megoldas[0]; ?>">
            <input type="text" name="helyes2" id="helyes2" value="<?php echo $megoldas[1]; ?>"><br>
            <input type="text" name="helyes3" id="helyes3" value="<?php echo $megoldas[2]; ?>"><br>

        </div>
        <input type="submit" name="submit" id="mentes" value="Mentés" class="alap_gomb">
    </form>
</main>

<script src="../tinymce/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
<script src="../tinymce/js/tinymce/plugins/tiny_mce_wiris/integration/WIRISplugins.js?viewer=image"></script>
<script>
tinymce.init({ 
    selector:'textarea' ,
    menu: {
        view: { title: 'View', items: 'code | visualaid visualchars visualblocks | preview ' },
        insert: { title: 'Insert', items: 'image link inserttable | charmap hr | nonbreaking ' }
    },
    plugins : 'tiny_mce_wiris table image code hr nonbreaking link lists advlist charmap visualchars visualblocks preview',
    toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | tiny_mce_wiris_formulaEditor | tiny_mce_wiris_formulaEditorChemistry',
    menubar: 'file edit view insert format table',
    advlist_bullet_styles: 'circle disc square',
    advlist_number_styles: 'default lower-alpha lower-greek lower-roman upper-alpha upper-roman'
});
</script>

<?php 
include("../includes/overall/footer.php"); 
include('../includes/overall/db_disconnect.php');
?>
