<?php

namespace App\Services;

/**
 * ZKLib - ZKTeco Device Communication Library
 * 
 * Ported from various open-source implementations.
 * Supports TCP connection to ZKTeco/Solution A200 devices.
 */
class ZKLib
{
    private $ip;
    private $port;
    private $socket = null;
    private $session_id = 0;
    private $reply_id = 0;

    const CMD_CONNECT        = 1000;
    const CMD_EXIT           = 1001;
    const CMD_ENABLEDEVICE   = 1002;
    const CMD_DISABLEDEVICE  = 1003;
    const CMD_ACK_OK         = 2000;
    const CMD_ACK_ERROR      = 2001;
    const CMD_ACK_DATA       = 2002;
    const CMD_PREPARE_DATA   = 1500;
    const CMD_DATA           = 1501;
    const CMD_FREE_DATA      = 1502;
    const CMD_ATTLOG_RRQ     = 13;
    const CMD_GET_TIME       = 201;

    public function __construct($ip, $port = 4370)
    {
        $this->ip   = $ip;
        $this->port = $port;
    }

    public function connect()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            return false;
        }

        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 3, 'usec' => 0]);
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, ['sec' => 3, 'usec' => 0]);

        if (!@socket_connect($this->socket, $this->ip, $this->port)) {
            return false;
        }

        // Send CONNECT command
        $command = $this->createHeader(self::CMD_CONNECT, '', $this->session_id, $this->reply_id);
        socket_write($this->socket, $command, strlen($command));

        $ret = socket_read($this->socket, 1024);
        if ($ret === false || strlen($ret) < 8) {
            return false;
        }

        $u = unpack('H2h1/H2h2/H2h3/H2h4/H2h5/H2h6/H2h7/H2h8', substr($ret, 0, 8));
        $session = hexdec($u['h6'] . $u['h5']);
        $this->session_id = $session;

        return true;
    }

    public function disconnect()
    {
        if ($this->socket) {
            $cmd = $this->createHeader(self::CMD_EXIT, '', $this->session_id, $this->reply_id);
            @socket_write($this->socket, $cmd, strlen($cmd));
            @socket_close($this->socket);
            $this->socket = null;
        }
    }

    public function getAttendance()
    {
        $command = $this->createHeader(self::CMD_ATTLOG_RRQ, '', $this->session_id, $this->reply_id);
        socket_write($this->socket, $command, strlen($command));

        $ret = $this->readResponse();
        if ($ret === false) {
            return [];
        }

        return $this->parseAttendanceData($ret);
    }

    public function disableDevice()
    {
        $cmd = $this->createHeader(self::CMD_DISABLEDEVICE, chr(0).chr(0), $this->session_id, $this->reply_id);
        return socket_write($this->socket, $cmd, strlen($cmd));
    }

    public function enableDevice()
    {
        $cmd = $this->createHeader(self::CMD_ENABLEDEVICE, '', $this->session_id, $this->reply_id);
        return socket_write($this->socket, $cmd, strlen($cmd));
    }

    private function createHeader($command, $command_string, $session_id, $reply_id)
    {
        $this->reply_id = ($this->reply_id + 1) % 0xFFFF;
        $buf = pack('SSSS', $command, 0, $session_id, $this->reply_id) . $command_string;
        $checksum = $this->createCheckSum($buf);
        $request_data = pack('SSSS', 0x5050, strlen($buf), $session_id, $this->reply_id);
        $request_data .= pack('SS', $command, $checksum) . $command_string;
        return $request_data;
    }

    private function createCheckSum($p)
    {
        $l = count(unpack('C*', $p));
        $checksum = 0;
        for ($i = 0; $i < $l; $i += 2) {
            if ($i == $l - 1) {
                $checksum += unpack('C', $p[$i])[1];
            } else {
                $u = unpack('S', substr($p, $i, 2));
                $checksum += $u[1];
                if ($checksum > 0xFFFF) {
                    $checksum -= 0xFFFF;
                }
            }
        }
        $checksum = ~$checksum;
        while ($checksum < 0) {
            $checksum += 0xFFFF;
        }
        return $checksum;
    }

    private function readResponse()
    {
        $ret_data = '';
        $bytes_recv = 0;
        
        // Read header (16 bytes)
        while ($bytes_recv < 16) {
            $tmp = socket_read($this->socket, 16 - $bytes_recv);
            if ($tmp === false || $tmp === '') break;
            $ret_data .= $tmp;
            $bytes_recv += strlen($tmp);
        }

        if ($bytes_recv < 16) {
            return false;
        }

        $u = unpack('H2h1/H2h2/H2h3/H2h4/H2h5/H2h6/H2h7/H2h8', substr($ret_data, 0, 8));
        $data_len = hexdec($u['h4'] . $u['h3']);

        if ($data_len > 8) {
            $remain = $data_len - 8;
            while ($remain > 0) {
                $tmp = socket_read($this->socket, $remain);
                if ($tmp === false || $tmp === '') break;
                $ret_data .= $tmp;
                $remain -= strlen($tmp);
            }
        }

        return $ret_data;
    }

    private function parseAttendanceData($data)
    {
        $attendance = [];
        if (strlen($data) < 16) {
            return $attendance;
        }

        $data = substr($data, 16); // Skip header
        $recordSize = 40;

        for ($i = 0; $i < strlen($data); $i += $recordSize) {
            if ($i + $recordSize > strlen($data)) break;

            $record = substr($data, $i, $recordSize);
            
            // User ID: first 9 bytes (null-terminated)
            $uid = rtrim(substr($record, 0, 9), "\x00");
            if (empty($uid)) continue;

            // Timestamp: 4 bytes at offset 10
            $ts_bytes = substr($record, 10, 4);
            $ts_arr = unpack('V', $ts_bytes);
            $timestamp = $ts_arr[1];

            // Convert from ZK epoch (2000-01-01) if needed
            if ($timestamp < 946684800) {
                $timestamp += 946684800;
            }

            $attendance[] = [
                'uid'      => $uid,
                'id'       => $uid,
                'state'    => ord($record[14]),
                'timestamp' => $timestamp,
                'datetime' => date('Y-m-d H:i:s', $timestamp),
            ];
        }

        return $attendance;
    }
}
