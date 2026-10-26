<?php
set_time_limit(30);
header('Content-Type: text/plain; charset=utf-8');

$ip      = '192.168.12.202';
$port    = 23;
$timeout = 5;

echo "=== ZEM560 Telnet Login ===\n\n";

function telnetRead($fp, $seconds = 2): string {
    $buf = '';
    $deadline = microtime(true) + $seconds;
    while (microtime(true) < $deadline) {
        $chunk = @fread($fp, 4096);
        if ($chunk && strlen($chunk) > 0) {
            // Strip telnet IAC sequences (0xFF ...)
            $chunk = preg_replace('/\xff[\xfb-\xfe]./s', '', $chunk);
            $chunk = preg_replace('/\xff\xff/s', "\xff", $chunk);
            $buf .= $chunk;
        }
        $m = stream_get_meta_data($fp);
        if ($m['timed_out']) break;
        usleep(100000);
    }
    return $buf;
}

function telnetSend($fp, string $data): void {
    fwrite($fp, $data);
}

$fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
if (!$fp) die("GAGAL: $errstr\n");
stream_set_timeout($fp, 3);
echo "Connected\n";

// Baca banner + login prompt
$banner = telnetRead($fp, 3);
echo "Banner: " . trim($banner) . "\n\n";

// Coba berbagai kombinasi login
$credentials = [
    ['root', ''],
    ['root', 'root'],
    ['root', 'admin'],
    ['admin', 'admin'],
    ['admin', ''],
    ['root', 'solokey'],
    ['root', 'zkteco'],
];

foreach ($credentials as [$user, $pass]) {
    echo "--- Coba login: user='$user' pass='" . ($pass ?: '(empty)') . "' ---\n";

    // Kirim username
    telnetSend($fp, $user . "\r\n");
    $r1 = telnetRead($fp, 2);
    echo "After username: " . trim($r1) . "\n";

    if (stripos($r1, 'password') !== false) {
        telnetSend($fp, $pass . "\r\n");
        $r2 = telnetRead($fp, 3);
        echo "After password: " . trim($r2) . "\n";

        if (stripos($r2, '#') !== false || stripos($r2, '$') !== false || stripos($r2, 'root@') !== false) {
            echo "\n*** LOGIN BERHASIL! Shell prompt ditemukan ***\n\n";

            // Coba baca attendance database
            echo "--- Mencari file database ---\n";
            telnetSend($fp, "find / -name '*.mdb' -o -name '*.db' -o -name 'att*' 2>/dev/null | head -20\r\n");
            echo telnetRead($fp, 5) . "\n";

            echo "--- ls /mnt/mtdblock ---\n";
            telnetSend($fp, "ls /mnt/mtdblock/ 2>/dev/null || ls /mnt/ 2>/dev/null\r\n");
            echo telnetRead($fp, 3) . "\n";

            echo "--- cat /att* atau attendance files ---\n";
            telnetSend($fp, "ls /att* /data* /root* 2>/dev/null\r\n");
            echo telnetRead($fp, 3) . "\n";

            fclose($fp);
            exit;
        }

        // Login gagal, baca prompt login lagi untuk percobaan berikutnya
        $prompt = telnetRead($fp, 2);
        echo "Next prompt: " . trim($prompt) . "\n";
    }
    echo "\n";
}

fclose($fp);
echo "=== Semua kredensial dicoba ===\n";
echo "Jika semua gagal, mesin mungkin punya password custom.\n";
