<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_start();

if (!isset($_SESSION["elkuldve"])) {

    $_SESSION["elkuldve"] = "elkuldve";
    include('../includes/overall/db_connect.php');

    $cim = $_SESSION["new_email"];
    $nev = $_SESSION["new_nev"];

    function generateRandomString() {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $randomString = "";
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    $str = generateRandomString();
    $_SESSION["pw"] = $str;

    date_default_timezone_set('Etc/UTC');

    require('PHPMailer-master/src/PHPMailer.php');
    require('PHPMailer-master/src/SMTP.php');
    require('PHPMailer-master/src/Exception.php');

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->Username = "wiqpm2@gmail.com";
        $mail->Password = "fcbr lqns oyzx adef";

        $mail->setFrom('wiqpm2@gmail.com', 'Szdg');
        $mail->addReplyTo('wiqpm2@gmail.com', 'Szdg');
        $mail->addAddress($cim, $nev);

        $mail->CharSet = "UTF-8";
        $mail->isHTML(true);
        $mail->Subject = 'Ideiglenes jelszó';

        $mail->msgHTML("
            <h3>Üdvözletem, $nev!</h3>
            <p>Önt regisztrálták az Szdg oldalon.</p>
            <p><strong>Ideiglenes jelszó: $str</strong></p>
        ");

        include('../includes/overall/db_disconnect.php');

        $mail->send();

        header("Location: create_acc1.php");
        exit();

    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        exit();
    }

} else {
    header("Location: ../fooldal/index.php");
    exit();
}
?>
