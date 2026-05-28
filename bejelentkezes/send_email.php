<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	session_start();
	if(!isset($_SESSION["elkuldve"])){
		$_SESSION["elkuldve"]="elkuldve";
		include('../includes/overall/db_connect.php');
		
			$stmt=$conn->prepare("SELECT * FROM felhasznalok WHERE email=?");
			$stmt->bind_param("s",$_SESSION["email"]);
			$stmt->execute();
			$result=$stmt->get_result();
			$row=$result->fetch_assoc();
			$cim=$row["email"];
			$nev=$row["nev"];
			$stmt->close();
			$conn->close();

		
		function generateRandomString(){
			$characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$randomString = "";
			for ($i=0; $i<16; $i++) {
				$randomString .= $characters[rand(0, strlen($characters)-1)];
			}
			return $randomString;
		}
		$str=generateRandomString();
		$_SESSION["pw"]=$str;
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

		$mail->msgHTML("<h3>Üdvözletem, ".$nev."!</h3><br>"."<h3>Ideiglenes jelszó: ".$str."</h3>");
		$mail->send();
		echo "<script>window.location.assign('change_password.php')</script>";
		
	} catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        exit();
    }
	}else{
		header("Location: ../fooldal/index.php");
		exit();
	}
?>