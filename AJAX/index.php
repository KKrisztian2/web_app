<?php
header("Content-Type: application/json; charset=utf-8");

$action = $_POST['action'] ?? '';
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "szakdolgzat_wiqpm2";

$conn = new mysqli($servername, $username, $password, $db_name);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if ($action === 'szintek') {
    $temakorok = json_decode($_POST['temakorok'] ?? '[]', true);
    if (empty($temakorok)) exit(json_encode([]));

    $temakorokEsc = array_map(fn($t) => "'" . mysqli_real_escape_string($conn, $t) . "'", $temakorok);

    $sql = "SELECT DISTINCT tipus 
            FROM feladatok 
            WHERE temakor IN (" . implode(",", $temakorokEsc) . ")
              AND nyilvanos = 1";

    $result = $conn->query($sql);
    $szintek = [];
    while ($row = $result->fetch_assoc()) {
        $szintek[] = (int)$row['tipus'];
    }

    echo json_encode($szintek);
    exit;
}

if ($action === 'feladatokSzama') {
    $temakorok = json_decode($_POST['temakorok'] ?? '[]', true);
    $szintek = json_decode($_POST['szintek'] ?? '[]', true);

    if (empty($temakorok) || empty($szintek)) {
        echo json_encode(0);
        exit;
    }

    $temakorokEsc = array_map(fn($t) => "'" . mysqli_real_escape_string($conn, $t) . "'", $temakorok);
    $szintekEsc = array_map('intval', $szintek);

    $sql = "SELECT COUNT(*) as cnt 
            FROM feladatok 
            WHERE temakor IN (" . implode(",", $temakorokEsc) . ") 
              AND tipus IN (" . implode(",", $szintekEsc) . ")
              AND nyilvanos = 1";  

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    echo json_encode((int)$row['cnt']);
    exit;
}

echo json_encode(["error" => "Invalid action"]);
?>
