<?php
set_time_limit(30);
header('Content-Type: text/plain; charset=utf-8');

$ip      = '192.168.12.202';
$port    = 23;
$timeout = 5;

echo "=== Probe Telnet Port 23 — $ip ===\n\n";

$fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
if (!$fp) {
    die("GAGAL: [$errno] $errstr\n");
}
stream_set_timeout($fp, 3);
echo "TERKONEKSI ke $ip:$port\n\n";

// Baca semua spontaneous data (greeting/banner)
echo "--- Spontaneous data (3 detik) ---\n";
$buf = '';
$deadline = microtime(true) + 3;
while (microtime(true) < $deadline) {
    $chunk = @fread($fp, 4096);
    if ($chunk && strlen($chunk) > 0) {
        $buf .= $chunk;
    }
    $m = stream_get_meta_data($fp);
    if ($m['timed_out']) break;
    usleep(100000);
}

if ($buf) {
    echo "Received " . strlen($buf) . " bytes:\n";
    echo "HEX: " . bin2hex($buf) . "\n";
    echo "ASCII: " . preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '.', $buf) . "\n\n";
} else {
    echo "Tidak ada spontaneous data\n\n";
}

// Coba kirim Enter
echo "--- Kirim Enter (0x0D 0x0A) ---\n";
fwrite($fp, "\r\n");
$resp = '';
$deadline = microtime(true) + 3;
while (microtime(true) < $deadline) {
    $chunk = @fread($fp, 4096);
    if ($chunk && strlen($chunk) > 0) { $resp .= $chunk; }
    $m = stream_get_meta_data($fp);
    if ($m['timed_out']) break;
    usleep(100000);
}
if ($resp) {
    echo "Response: " . preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '.', $resp) . "\n";
    echo "HEX: " . bin2hex($resp) . "\n\n";
} else {
    echo "Tidak ada response\n\n";
}

// Coba command umum
foreach (['help', '?', 'info', 'status', 'ver', 'version', 'GET INFO'] as $cmd) {
    echo "--- Kirim: $cmd ---\n";
    fwrite($fp, $cmd . "\r\n");
    $r = ''; $d = microtime(true) + 2;
    while (microtime(true) < $d) {
        $c = @fread($fp, 4096);
        if ($c && strlen($c)>0) { $r .= $c; }
        $mm = stream_get_meta_data($fp); if ($mm['timed_out']) break;
        usleep(100000);
    }
    if ($r) {
        echo "Response: " . preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '.', $r) . "\n";
    } else {
        echo "No response\n";
    }
    echo "\n";
}

fclose($fp);
echo "=== SELESAI ===\n";
