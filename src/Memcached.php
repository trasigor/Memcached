<?php

namespace Trasigor\Memcached;

class Memcached {

    protected $resource;

    public function __construct($host = '127.0.0.1', $port = 11211) {
        if (!$this->resource = @fsockopen('tcp://' . $host, $port, $errno, $errstr)) {
            throw new \Exception('Unable to connect to tcp://' . $host . ':' . $port . ' with error: ' . $errstr);
        }

        if ($info = stream_get_meta_data($this->resource) && !empty($info['timeout'])) {
            throw new \Exception('Unable to connect: Connection timed out...');
        }
    }

    public function get($key) {
        $this->check_key($key);

        fwrite($this->resource, "get $key\n");

        $buffer = trim(fread($this->resource, 4096));

        $this->check_errors($buffer);

        if (preg_match("/^VALUE\s+$key\s+\d+\s+(\d+)\r\n/", $buffer, $match)) {
            if (preg_match("/^VALUE\s+$key\s+\d+\s+\d+\r\n(.{{$match[1]}})\r\nEND$/", $buffer, $match)) {
                return unserialize($match[1]);
            }
        }

        return null;
    }

    public function set($key, $val, $exptime  = 3600, $flags = 0) {
        $this->check_key($key);

        fwrite($this->resource, "set $key $flags $exptime ".strlen(serialize($val))."\r\n");
        fwrite($this->resource, serialize($val)."\r\n");

        $buffer = trim(fread($this->resource, 4096));

        $this->check_errors($buffer);

        if (!strcmp("STORED", $buffer)) {
            return true;
        } elseif (!strcmp("NOT_STORED", $buffer)) {
            throw new \Exception("Variable with key \"$key\" not stored");
        }

        return false;
    }

    public function delete($key) {
        $this->check_key($key);

        fwrite($this->resource, "delete $key\n");

        $buffer = trim(fread($this->resource, 4096));

        $this->check_errors($buffer);

        if (!strcmp('DELETED', $buffer)) {
            return true;
        } elseif (!strcmp('NOT_FOUND', $buffer)) {
            throw new \Exception("Variable with key \"$key\" not found");
        }

        return false;
    }

    public function disconnect() {
        @fclose($this->resource);
    }

    public function __destruct() {
        $this->disconnect();
    }

    protected function check_key($key) {
        if (!is_string($key) || preg_match("/[\s|\r|\n|\r\n]/", $key) || strlen($key) < 1 || strlen($key) > 250) {
            throw new \Exception("The key should be a string with maximum length of 250 characters and must not include control characters or whitespace.");
        }
    }

    protected function check_errors($buffer) {
        if (preg_match("/^CLIENT_ERROR\s+(.+)\r\nERROR$/", $buffer, $match)) {
            throw new \Exception($match[1]);
        } elseif (preg_match("/^SERVER_ERROR\s+(.+)\r\nERROR$/", $buffer, $match)) {
            throw new \Exception($match[1]);
        } elseif (is_int(strpos("ERROR", $buffer))) {
            throw new \Exception("Error occurred.");
        }
    }
}