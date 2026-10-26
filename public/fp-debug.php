<?php
/**
 * Fingerprint Debug — Solution A200
 * Akses: http://localhost/HRGAapp-main/public/fp-debug.php?ip=IP_MESIN
 * HAPUS file ini setelah debug selesai!
 */
set_time_limit(15);

$ip      = $_GET['ip']      ?? '';
$port    = (int)($_GET['port']    ?? 4370);
$timeout = (int)($_GET['timeout'] ?? 5);

if (!$ip) {
    die("Tambahkan ?ip=IP_MESIN di URL\nContoh: ?ip=192.168.1.100");
}

header('Content-Type: text/plain; charset=utf-8');
echo "=================================================\n";
echo "  Fingerprint Debug — Solution A200\n";
echo "=================================================\n";
echo "IP      : $ip\n";
echo "Port    : $port\n";
echo "Timeout : $timeout detik\n\n";

// -------------------------------------------------------
// Helper: buat ZKTeco packet
// -------------------------------------------------------
function makePacket(int $cmd, string $data, int $sessionId, int &$replyId): string
{
    $replyId = ($replyId + 1) & 0xFFFF;
    $size    = strlen($data) + 8;

    // Checksum buffer
    $buf = pack('SSSS', $size, $sessionId, $replyId, $cmd) . $data;
    $sum = 0;
    $len = strlen($buf);
    for ($i = 0; $i + 1 < $len; $i += 2) {
        $sum += unpack('S', substr($buf, $i, 2))[1];
        if ($sum > 0xFFFF) $sum -= 0xFFFF;
    }
    if ($len % 2 !== 0) $sum += ord($buf[$len - 1]);
    $sum = ~$sum;
    while ($sum < 0) $sum += 0xFFFF;
    $checksum = $sum & 0xFFFF;

    return pack('SSSSSS', 0x5050, $size, $sessionId, $replyId, $cmd, $checksum) . $data;
}

function hexDump(string $data, int $maxBytes = 128): string
{
    $hex = bin2hex(substr($data, 0, $maxBytes));
    $spaced = implode(' ', str_split($hex, 2));
    $ascii = preg_replace('/[^\x20-\x7E]/', '.', substr($data, 0, $maxBytes));
    return "HEX: $spaced\nASC: $ascii";
}

// -------------------------------------------------------
// Test 1: TCP connect
// -------------------------------------------------------
echo "-----------------------------------------------\n";
echo "TEST 1: TCP Connect ke $ip:$port\n";
echo "-----------------------------------------------\n";

$errno = 0; $errstr = '';
$fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);

if (!$fp) {
    echo "GAGAL konek: [$errno] $errstr\n";
    echo "\nKemungkinan:\n";
    echo "  - IP salah\n";
    echo "  - Port $port tidak terbuka\n";
    echo "  - Firewall memblokir\n";
    exit;
}

echo "BERHASIL konek ke $ip:$port\n\n";
stream_set_timeout($fp, $timeout);

// Cek apakah ada spontaneous data (beberapa device kirim greeting)
stream_set_timeout($fp, 1);
$greeting = @fread($fp, 1024);
if ($greeting && strlen($greeting) > 0) {
    echo "Device mengirim GREETING " . strlen($greeting) . " bytes:\n";
    echo hexDump($greeting) . "\n\n";
} else {
    echo "Tidak ada spontaneous data (normal)\n\n";
}
stream_set_timeout($fp, $timeout);

// -------------------------------------------------------
// Test 2: CMD_CONNECT (1000)
// -------------------------------------------------------
echo "-----------------------------------------------\n";
echo "TEST 2: Kirim CMD_CONNECT (1000)\n";
echo "-----------------------------------------------\n";

$sessionId = 0;
$replyId   = 0;
$pkt = makePacket(1000, '', $sessionId, $replyId);

echo "Mengirim " . strlen($pkt) . " bytes:\n";
echo hexDump($pkt) . "\n";

fwrite($fp, $pkt);

$resp = @fread($fp, 65535);
$meta = stream_get_meta_data($fp);

if (!$resp || strlen($resp) === 0) {
    echo "\nTidak ada response" . ($meta['timed_out'] ? " (TIMEOUT)" : "") . "\n";
} else {
    echo "\nResponse " . strlen($resp) . " bytes:\n";
    echo hexDump($resp) . "\n";

    if (strlen($resp) >= 12) {
        $h = unpack('Sstart/Ssize/Ssession/Sreply/Scmd/Schecksum', substr($resp, 0, 12));
        $h = array_map(fn($v) => $v & 0xFFFF, $h);
        echo "\nParsed:\n";
        echo "  start    = 0x" . dechex($h['start']) . " (expect 0x5050)\n";
        echo "  size     = " . $h['size'] . "\n";
        echo "  session  = " . $h['session'] . "\n";
        echo "  reply    = " . $h['reply'] . "\n";
        echo "  cmd      = " . $h['cmd'] . " (2000=ACK_OK, 2001=ACK_ERROR)\n";
        echo "  checksum = " . $h['checksum'] . "\n";

        if ($h['cmd'] === 2000) {
            echo "\n✓ HANDSHAKE BERHASIL — session_id=" . $h['session'] . "\n";
            $sessionId = $h['session'];
        } elseif ($h['cmd'] === 2001) {
            echo "\n✗ Device menolak (ACK_ERROR). Cek Comm Key di mesin.\n";
        } else {
            echo "\n? Cmd tidak dikenal: " . $h['cmd'] . "\n";
        }
    }
}

// -------------------------------------------------------
// Test 3: CMD_GET_TIME (201) — simple read
// -------------------------------------------------------
echo "\n-----------------------------------------------\n";
echo "TEST 3: Kirim CMD_GET_TIME (201)\n";
echo "-----------------------------------------------\n";

$pkt2 = makePacket(201, '', $sessionId, $replyId);
fwrite($fp, $pkt2);
$resp2 = @fread($fp, 65535);

if (!$resp2 || strlen($resp2) === 0) {
    $m = stream_get_meta_data($fp);
    echo "Tidak ada response" . ($m['timed_out'] ? " (TIMEOUT)" : "") . "\n";
} else {
    echo "Response " . strlen($resp2) . " bytes:\n";
    echo hexDump($resp2) . "\n";
    if (strlen($resp2) >= 16) {
        $h2 = unpack('Sstart/Ssize/Ssession/Sreply/Scmd/Schecksum', substr($resp2, 0, 12));
        $h2 = array_map(fn($v) => $v & 0xFFFF, $h2);
        echo "cmd=" . $h2['cmd'] . "\n";
        if (strlen($resp2) >= 16) {
            $ts = unpack('V', substr($resp2, 12, 4))[1];
            echo "Timestamp raw: $ts\n";
            // ZK timestamp decode
            $second = $ts & 0x1F;
            $minute = ($ts >> 5) & 0x3F;
            $hour   = ($ts >> 11) & 0x1F;
            $day    = ($ts >> 16) & 0x1F;
            $month  = ($ts >> 21) & 0x0F;
            $year   = (($ts >> 26) & 0x3F) + 2000;
            echo "ZK Time: $year-" . str_pad($month,2,'0',STR_PAD_LEFT) . "-" . str_pad($day,2,'0',STR_PAD_LEFT)
               . " " . str_pad($hour,2,'0',STR_PAD_LEFT) . ":" . str_pad($minute,2,'0',STR_PAD_LEFT)
               . ":" . str_pad($second,2,'0',STR_PAD_LEFT) . "\n";
        }
    }
}

// -------------------------------------------------------
// Test 4: Port 80 — HTTP check
// -------------------------------------------------------
fclose($fp);

echo "\n-----------------------------------------------\n";
echo "TEST 4: Cek HTTP di $ip:80\n";
echo "-----------------------------------------------\n";

$fp80 = @fsockopen($ip, 80, $errno80, $errstr80, 3);
if (!$fp80) {
    echo "Port 80 tidak terbuka: [$errno80] $errstr80\n";
} else {
    echo "Port 80 TERBUKA\n";
    fwrite($fp80, "GET / HTTP/1.0\r\nHost: $ip\r\n\r\n");
    stream_set_timeout($fp80, 2);
    $http = @fread($fp80, 512);
    fclose($fp80);
    if ($http) {
        echo "HTTP response: " . substr($http, 0, 200) . "\n";
    } else {
        echo "Port 80 terbuka tapi tidak ada HTTP response\n";
    }
}

echo "\n=================================================\n";
echo "  SELESAI — Hapus file ini setelah debug!\n";
echo "=================================================\n";
