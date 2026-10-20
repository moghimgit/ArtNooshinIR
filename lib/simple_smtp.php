<?php
function send_smtp_mail(string $to, string $subject, string $htmlBody, string $replyTo = ''): bool
{
    $host = SMTP_HOST;
    $port = SMTP_PORT;
    $user = SMTP_USER;
    $pass = SMTP_PASS;
    $from = SMTP_USER;

    $context = stream_context_create([
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true]
    ]);

    $prefix = SMTP_SECURE === 'ssl' ? 'ssl://' : '';
    $socket = @stream_socket_client("$prefix$host:$port", $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
    if (!$socket) return false;

    $read = function() use ($socket) {
        $data = '';
        while ($line = fgets($socket, 515)) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $data;
    };
    $write = function($cmd) use ($socket) { fwrite($socket, $cmd . "\r\n"); };
    $expect = function($code) use ($read) {
        $resp = $read();
        return substr($resp, 0, 3) === (string)$code;
    };

    if (!$expect(220)) { fclose($socket); return false; }

    $write("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    $read();

    if (SMTP_SECURE === 'tls') {
        $write("STARTTLS");
        if (!$expect(220)) { fclose($socket); return false; }
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $write("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
        $read();
    }

    $write("AUTH LOGIN");
    if (!$expect(334)) { fclose($socket); return false; }
    $write(base64_encode($user));
    if (!$expect(334)) { fclose($socket); return false; }
    $write(base64_encode($pass));
    if (!$expect(235)) { fclose($socket); return false; }

    $write("MAIL FROM:<$from>");
    if (!$expect(250)) { fclose($socket); return false; }

    $write("RCPT TO:<$to>");
    if (!$expect(250)) { fclose($socket); return false; }

    $write("DATA");
    if (!$expect(354)) { fclose($socket); return false; }

    $headers  = "From: " . SITE_NAME . " <$from>\r\n";
    $headers .= "To: <$to>\r\n";
    if ($replyTo) $headers .= "Reply-To: <$replyTo>\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: base64\r\n";

    $write($headers . "\r\n" . chunk_split(base64_encode($htmlBody)) . "\r\n.");
    if (!$expect(250)) { fclose($socket); return false; }

    $write("QUIT");
    fclose($socket);
    return true;
}