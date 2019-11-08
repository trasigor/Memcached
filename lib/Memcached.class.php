<?php

namespace Trasigor\Memcached;

class Memcached {

    protected $telnet;

    public function __construct($host = '127.0.0.1', $port = 11211) {
        if (!$this->telnet = @fsockopen('tcp://' . $host, $port, $errno, $errstr)) {
            throw new \Exception('Unable to connect to tcp://' . $host . ':' . $port . ' with error: ' . $errstr);
        }

        if ($info = stream_get_meta_data($this->telnet) && !empty($info['timeout'])) {
            throw new \Exception('Unable to connect: Connection timed out...');
        }
    }

    public function get($key) {
        fwrite($this->telnet, "get $key\n");

        $buffer = trim(fread($this->telnet, 4096));

        if (preg_match("/^VALUE[^\n]+\n(.+)\nEND$/", $buffer, $match)) {
            return $match[1];
        } elseif (is_int(strpos("ERROR", $buffer))) {
            throw new \Exception("Error occurred during fetching variable with key \"$key\"");
        }

        return null;
    }

    public function set($key, $val, $exptime  = 3600, $flags = 0) {
        fwrite($this->telnet, "set $key $flags $exptime ".strlen($val)."\r\n");
        fwrite($this->telnet, "$val\r\n");

        $buffer = trim(fread($this->telnet, 4096));

        if (!strcmp('STORED', $buffer)) {
            return true;
        } elseif (!strcmp('NOT_STORED', $buffer)) {
            throw new \Exception("Variable with key \"$key\" not stored");
        } elseif (is_int(strpos("ERROR", $buffer))) {
            throw new \Exception("Error occurred during saving variable with key \"$key\"");
        }

        return false;
    }

    public function delete($key) {
        fwrite($this->telnet, "delete $key\n");

        $buffer = trim(fread($this->telnet, 4096));

        if (!strcmp('DELETED', $buffer)) {
            return true;
        } elseif (!strcmp('NOT_FOUND', $buffer)) {
            throw new \Exception("Variable with key \"$key\" not found");
        } elseif (is_int(strpos("ERROR", $buffer))) {
            throw new \Exception("Error occurred during deleting variable with key \"$key\"");
        }

        return false;
    }

    public function __destruct()
    {
        try
        {
            fclose($this->telnet);
        }
        catch ( \Exception $e )
        {
            return;
        }
    }
}