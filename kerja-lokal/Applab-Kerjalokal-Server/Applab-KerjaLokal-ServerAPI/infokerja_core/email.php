<?
include('../Mail-1.2.0/Mail-1.2.0/Mail.php');

$from = "Yudha Hafiedz <yudha_hafiedz@yahoo.com>";
$to = "Yudha Altermyth <yudha@altermyth.com>";
$subject = "This is a test message";
$body = "Did this work?";

$host = "ssl://smtp.gmail.com";
$port = "465";
$username = "support@kerjalokal.com";
$password = "support@kerjalokal1";

$headers = array ('From' => $from,
  'To' => $to,
  'Subject' => $subject,
  'MIME-Version' => "1.0",
  'Content-type' => "text/html; charset=iso-8859-1"
  );
$smtp = Mail::factory('smtp',
  array ('host' => $host,
    'port' => $port,
    'auth' => true,
    'username' => $username,
    'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
  echo("<p>" . $mail->getMessage() . "</p>");
 } else {
  echo("<p>Message successfully sent!</p>");
 }



?>