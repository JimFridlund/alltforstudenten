<?php
class Mailer {

  public static function send($to, $subject, $htmlBody, $textBody=null){
    $to = trim((string)$to);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return [false, "Invalid recipient email."];

    $from = defined('VIDDRA_MAIL_FROM') ? VIDDRA_MAIL_FROM : 'no-reply@localhost';
    $fromName = defined('VIDDRA_MAIL_FROM_NAME') ? VIDDRA_MAIL_FROM_NAME : 'Viddra';

    $subject = trim((string)$subject);
    if ($subject === '') $subject = 'Message from Viddra';

    $driver = defined('VIDDRA_MAIL_DRIVER') ? VIDDRA_MAIL_DRIVER : 'mail';

    if ($driver !== 'mail') {
      // Placeholder for SMTP integration (PHPMailer etc.)
      return [false, "SMTP driver not implemented in this step. Set VIDDRA_MAIL_DRIVER to 'mail'."];
    }

    // Build headers for HTML email
    $headers = [];
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-type:text/html;charset=UTF-8";
    $headers[] = "From: " . self::encodeHeader($fromName) . " <{$from}>";
    $headers[] = "Reply-To: {$from}";
    $headersStr = implode("\r\n", $headers);

    $ok = @mail($to, self::encodeHeader($subject), $htmlBody, $headersStr);
    if (!$ok) return [false, "mail() failed on server. Configure hosting mail or use SMTP later."];

    return [true, null];
  }

  private static function encodeHeader($s){
    $s = (string)$s;
    // best-effort RFC2047 for UTF-8
    return "=?UTF-8?B?" . base64_encode($s) . "?=";
  }
}
?>
