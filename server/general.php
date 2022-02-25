<?php

require_once '../libraries/php-mailer/PHPMailerAutoload.php';

$fromEmail = 'mypresto@info.com';
$fromName = 'MY PRESTO';

function sendEmail($fromEmail, $fromName, $toEmail, $toName, $subject, $htmlContent){
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->CharSet = 'UTF-8';

	$mail->SMTPSecure = "tls";
	$mail->Host = "premium52.web-hosting.com";
	$mail->Username = "pedidos@my-presto.com";
	$mail->Password = "20ingresos19001";
	$mail->Port = 465;

	$mail->From = $fromEmail;
	$mail->FromName = $fromName;
	$mail->Subject = $subject;
	$mail->IsHTML(true);

	$mail->AddAddress($toEmail, $toName);
	$mail->Body = $htmlContent;

	$mail->Send();
}

?>