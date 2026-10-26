<?php
/**
 * Fingerprint Protocol Probe — coba berbagai format packet
 * Untuk mesin dengan Password=0, No Mesin=1
 */
set_time_limit(60);
header('Content-Type: text/plain; charset=utf-8');

$ip      = '192.168.12.202';
$port    = 4370;
$timeout = 4;

echo "Probing $ip:$port\n\n";

function tryPacket(string $label, string $packet, string $ip, int $port, int $timeout): void
{
    echo "--- $label ---\n";
    echo "Sent (" . strlen($packet) . " bytes): " . bin2hex($packet) . "\n";

    $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
    if (!$fp) {
        echo "Connect FAIL: $errstr\n\n";
        return;
    }
    stream_set_timeout($fp, $timeout);
    fwrite($fp, $packet);

    $resp = '';
    $deadline = microtime(true) + $timeout;
    while (microtime(true) < $deadline) {
        $chunk = @fread($fp, 65535);
        if ($chunk && strlen($chunk) > 0) {
            $resp .= $chunk;
            break;
        }
        $m = stream_get_meta_data($fp);
        if ($m['timed_out'] || feof($fp)) break;
        usleep(200000);
    }
    fclose($fp);

    if (!$resp) {
        echo "Response: NONE / TIMEOUT\n\n";
        return;
    }

    echo "Response (" . strlen($resp) . " bytes): " . bin2hex($resp) . "\n";
    echo "ASCII: " . preg_replace('/[^\x20-\x7E]/', '.', $resp) . "\n";

    if (strlen($resp) >= 8) {
        // Try to parse as ZKTeco-style
        $vals = unpack('v4', substr($resp, 0, 8));
        echo "Words[0..3]: " . implode(', ', $vals) . "\n";
    }
    echo "\n";
}

// -------------------------------------------------------
// Variasi 1 — Format standar ZKTeco (yang sudah kita coba)
// CMD=1000 (0x03E8), session=0, reply=1
// pack: start(2) size(2) session(2) reply(2) cmd(2) checksum(2)
// -------------------------------------------------------
$pkt1 = pack('vvvvvv', 0x5050, 8, 0, 1, 1000, 0);
// recalc checksum
$buf = pack('vvvv', 8, 0, 1, 1000);
$s = 0; for ($i=0;$i<8;$i+=2) { $s += unpack('v',substr($buf,$i,2))[1]; if($s>0xFFFF) $s-=0xFFFF; }
$s=~$s; while($s<0) $s+=0xFFFF; $s &= 0xFFFF;
$pkt1 = pack('vvvvvv', 0x5050, 8, 0, 1, 1000, $s);
tryPacket('Format A: start(0x5050) size session reply cmd checksum', $pkt1, $ip, $port, $timeout);

// -------------------------------------------------------
// Variasi 2 — Format alternatif: reply=0xFFFF (beberapa firmware)
// -------------------------------------------------------
$buf2 = pack('vvvv', 8, 0, 0xFFFF, 1000);
$s2 = 0; for ($i=0;$i<8;$i+=2) { $s2 += unpack('v',substr($buf2,$i,2))[1]; if($s2>0xFFFF) $s2-=0xFFFF; }
$s2=~$s2; while($s2<0) $s2+=0xFFFF; $s2 &= 0xFFFF;
$pkt2 = pack('vvvvvv', 0x5050, 8, 0, 0xFFFF, 1000, $s2);
tryPacket('Format B: reply=0xFFFF', $pkt2, $ip, $port, $timeout);

// -------------------------------------------------------
// Variasi 3 — No checksum (beberapa mesin lama abaikan)
// -------------------------------------------------------
$pkt3 = pack('vvvvvv', 0x5050, 8, 0, 1, 1000, 0);
tryPacket('Format C: checksum=0 (no checksum)', $pkt3, $ip, $port, $timeout);

// -------------------------------------------------------
// Variasi 4 — 16 byte header (beberapa firmware pakai header lebih panjang)
// -------------------------------------------------------
$pkt4 = pack('vvvvvvvv', 0x5050, 16, 0, 1, 1000, 0, 0, 0);
tryPacket('Format D: 16-byte header', $pkt4, $ip, $port, $timeout);

// -------------------------------------------------------
// Variasi 5 — Dengan password "0" di data field
// -------------------------------------------------------
$data5 = pack('V', 0); // password = 0 as uint32
$size5 = 8 + strlen($data5);
$buf5  = pack('vvvv', $size5, 0, 1, 1000) . $data5;
$s5 = 0; $l5=strlen($buf5);
for ($i=0;$i+1<$l5;$i+=2) { $s5+=unpack('v',substr($buf5,$i,2))[1]; if($s5>0xFFFF) $s5-=0xFFFF; }
$s5=~$s5; while($s5<0) $s5+=0xFFFF; $s5 &= 0xFFFF;
$pkt5 = pack('vvvvvv', 0x5050, $size5, 0, 1, 1000, $s5) . $data5;
tryPacket('Format E: dengan password=0 di data', $pkt5, $ip, $port, $timeout);

// -------------------------------------------------------
// Variasi 6 — Raw minimal: hanya 0x0000 (beberapa mesin reply ke apapun)
// -------------------------------------------------------
tryPacket('Format F: raw 0x0000 (minimal probe)', "\x00\x00", $ip, $port, $timeout);

// -------------------------------------------------------
// Variasi 7 — HTTP GET (cek apakah port 4370 juga serve HTTP)
// -------------------------------------------------------
$httpReq = "GET / HTTP/1.0\r\nHost: $ip\r\n\r\n";
tryPacket('Format G: HTTP GET (kalau port 4370 serve HTTP juga)', $httpReq, $ip, $port, $timeout);

// -------------------------------------------------------
// Variasi 8 — Cek port lain yang umum dipakai mesin absen
// -------------------------------------------------------
echo "=== Cek port lain ===\n";
foreach ([80, 8080, 8000, 4000, 5000, 23] as $p) {
    $t = @fsockopen($ip, $p, $e, $es, 1);
    echo "Port $p: " . ($t ? "TERBUKA" : "TERTUTUP ($es)") . "\n";
    if ($t) fclose($t);
}

echo "\n=== SELESAI ===\n";
