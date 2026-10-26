<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * FingerprintService — Solution A200 / ZKTeco SOAP via HTTP
 *
 * Mesin Solution A200 menggunakan HTTP SOAP pada port 80.
 * Endpoint: POST http://<ip>:<port>/iWsService
 *
 * Referensi: https://github.com/FAHRIZTX/ZKSoapPHP
 */
class FingerprintService
{
    protected string $ip;
    protected int    $port;
    protected int    $timeout;
    protected string $commKey;

    const ENDPOINT = '/iWsService';
    const CRLF     = "\r\n";

    public function __construct(string $ip, int $port = 80, int $timeout = 10, $commKey = '0')
    {
        $this->ip      = $ip;
        $this->port    = $port;
        $this->timeout = $timeout;
        $this->commKey = (string) $commKey;
    }

    // =========================================================
    // PUBLIC API
    // =========================================================

    /**
     * Test koneksi ke mesin.
     */
    public function testConnection(): array
    {
        try {
            $fp = $this->openConnection();
            fclose($fp);
            return ['success' => true, 'message' => 'Connection successful'];
        } catch (Exception $e) {
            Log::warning('[FP] testConnection failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Ambil semua attendance log dari mesin.
     * Return array of: ['pin', 'datetime', 'verified', 'status', 'workcode']
     */
    public function getAttendanceLogs(string $pin = 'all', ?string $dateStart = null, ?string $dateEnd = null): array
    {
        try {
            if ($dateStart !== null && $dateEnd === null) {
                $dateEnd = $dateStart;
            }

            $pinXml = is_array($pin)
                ? implode('', array_map(fn($p) => "<Arg><PIN>{$p}</PIN></Arg>", $pin))
                : "<Arg><PIN>{$pin}</PIN></Arg>";

            $payload = '<GetAttLog>'
                . '<ArgComKey xsi:type="xsd:integer">' . $this->commKey . '</ArgComKey>'
                . $pinXml
                . '</GetAttLog>';

            $rawXml = $this->soapRequest($payload);

            Log::info('[FP] getAttendanceLogs raw XML length: ' . strlen($rawXml));

            $logs = $this->parseAttendanceLogs($rawXml, $dateStart, $dateEnd);

            Log::info('[FP] getAttendanceLogs: ' . count($logs) . ' records');
            return $logs;

        } catch (Exception $e) {
            Log::error('[FP] getAttendanceLogs error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil log terbaru (1 record paling baru hari ini).
     */
    public function getLatestLog(): ?array
    {
        $today = date('Y-m-d');
        $logs  = $this->getAttendanceLogs('all', $today, $today);

        if (empty($logs)) {
            return null;
        }

        // Urutkan descending by datetime, ambil yang paling baru
        usort($logs, fn($a, $b) => strcmp($b['datetime'], $a['datetime']));
        return $logs[0];
    }

    /**
     * Sync semua log hari ini.
     */
    public function syncNow(): array
    {
        $today = date('Y-m-d');
        $logs  = $this->getAttendanceLogs('all', $today, $today);

        return [
            'success'    => true,
            'message'    => 'Sync completed',
            'total_logs' => count($logs),
            'logs'       => $logs,
        ];
    }

    /**
     * Cek status device: online atau offline.
     */
    public function getDeviceStatus(): array
    {
        $r = $this->testConnection();
        return [
            'online' => $r['success'],
            'status' => $r['success'] ? 'Online' : 'Offline',
            'ip'     => $this->ip,
            'port'   => $this->port,
        ];
    }

    /**
     * Ambil daftar user dari mesin.
     */
    public function getUsers($pin = 'all'): array
    {
        try {
            $pinXml = is_array($pin)
                ? implode('', array_map(fn($p) => "<Arg><PIN>{$p}</PIN></Arg>", $pin))
                : "<Arg><PIN>{$pin}</PIN></Arg>";

            $payload = '<GetUserInfo>'
                . '<ArgComKey xsi:type="xsd:integer">' . $this->commKey . '</ArgComKey>'
                . $pinXml
                . '</GetUserInfo>';

            $rawXml = $this->soapRequest($payload);
            return $this->parseUserInfo($rawXml);

        } catch (Exception $e) {
            Log::error('[FP] getUsers error: ' . $e->getMessage());
            return [];
        }
    }

    // =========================================================
    // HTTP/SOAP
    // =========================================================

    /**
     * Buka koneksi TCP ke mesin.
     * @return resource
     */
    protected function openConnection()
    {
        $errno  = 0;
        $errstr = '';

        $fp = @fsockopen($this->ip, $this->port, $errno, $errstr, $this->timeout);

        if ($fp === false) {
            throw new Exception("Cannot connect to {$this->ip}:{$this->port} — [{$errno}] {$errstr}");
        }

        stream_set_timeout($fp, $this->timeout);
        return $fp;
    }

    /**
     * Kirim SOAP request dan kembalikan response body.
     */
    protected function soapRequest(string $xmlPayload): string
    {
        $fp = $this->openConnection();

        $len = strlen($xmlPayload);

        // HTTP request headers
        $request  = "POST " . self::ENDPOINT . " HTTP/1.0" . self::CRLF;
        $request .= "Host: {$this->ip}" . self::CRLF;
        $request .= "Content-Type: text/xml" . self::CRLF;
        $request .= "Content-Length: {$len}" . self::CRLF;
        $request .= self::CRLF;
        $request .= $xmlPayload . self::CRLF;

        fputs($fp, $request);

        // Read full response
        $response = '';
        while (!feof($fp)) {
            $chunk = fgets($fp, 4096);
            if ($chunk === false) {
                break;
            }
            $response .= $chunk;
        }

        fclose($fp);

        Log::debug('[FP] soapRequest response length: ' . strlen($response));

        // Extract body (after double CRLF header separator)
        $pos = strpos($response, self::CRLF . self::CRLF);
        if ($pos !== false) {
            return substr($response, $pos + 4);
        }

        return $response;
    }

    // =========================================================
    // PARSERS
    // =========================================================

    protected function parseAttendanceLogs(string $xml, ?string $dateStart, ?string $dateEnd): array
    {
        $logs = [];

        if (empty($xml)) {
            return $logs;
        }

        // Extract between <GetAttLogResponse> ... </GetAttLogResponse>
        $start = strpos($xml, '<GetAttLogResponse>');
        $end   = strpos($xml, '</GetAttLogResponse>');
        if ($start !== false && $end !== false) {
            $xml = substr($xml, $start, $end - $start + strlen('</GetAttLogResponse>'));
        }

        // Split by <Row>
        $rows = explode('<Row>', $xml);
        array_shift($rows); // remove preamble

        foreach ($rows as $row) {
            $row = explode('</Row>', $row)[0];

            $pin      = $this->extractTag($row, 'PIN');
            $datetime = $this->extractTag($row, 'DateTime');
            $verified = $this->extractTag($row, 'Verified');
            $status   = $this->extractTag($row, 'Status');
            $workcode = $this->extractTag($row, 'WorkCode');

            if ($pin === '' || $datetime === '') {
                continue;
            }

            // Date filter
            if ($dateStart !== null && $dateEnd !== null) {
                $recordDate = explode(' ', $datetime)[0];
                $range      = $this->buildDateRange($dateStart, $dateEnd);
                if (!in_array($recordDate, $range)) {
                    continue;
                }
            }

            $logs[] = [
                'pin'      => $pin,
                'datetime' => $datetime,
                'verified' => $verified,
                'status'   => $status,
                'workcode' => $workcode,
            ];
        }

        return $logs;
    }

    protected function parseUserInfo(string $xml): array
    {
        $users = [];

        if (empty($xml)) {
            return $users;
        }

        $rows = explode('<Row>', $xml);
        array_shift($rows);

        foreach ($rows as $row) {
            $row = explode('</Row>', $row)[0];

            $users[] = [
                'pin'       => $this->extractTag($row, 'PIN'),
                'name'      => $this->extractTag($row, 'Name'),
                'password'  => $this->extractTag($row, 'Password'),
                'group'     => $this->extractTag($row, 'Group'),
                'privilege' => $this->extractTag($row, 'Privilege'),
                'card'      => $this->extractTag($row, 'Card'),
                'pin2'      => $this->extractTag($row, 'PIN2'),
            ];
        }

        return $users;
    }

    protected function extractTag(string $xml, string $tag): string
    {
        $open  = "<{$tag}>";
        $close = "</{$tag}>";

        $start = strpos($xml, $open);
        if ($start === false) {
            return '';
        }

        $start += strlen($open);
        $end = strpos($xml, $close, $start);
        if ($end === false) {
            return '';
        }

        return substr($xml, $start, $end - $start);
    }

    protected function buildDateRange(string $start, string $end): array
    {
        $range  = [];
        $period = new \DatePeriod(
            new \DateTime($start),
            new \DateInterval('P1D'),
            (new \DateTime($end))->modify('+1 day')
        );
        foreach ($period as $date) {
            $range[] = $date->format('Y-m-d');
        }
        return $range;
    }
}
